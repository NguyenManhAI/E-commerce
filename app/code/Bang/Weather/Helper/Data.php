<?php
namespace Bang\Weather\Helper;

use Bang\Weather\Model\WeatherInfo;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Data extends AbstractHelper 
{
    const API_KEY_PATH = "api/config/key";
    const CITY_DEFAULT_PATH = "api/city/city1";
    const CACHE_LIFETIME = 3600; // Cache for 30 minutes

    protected $apiKey;
    protected $city;
    protected $weatherInfo;
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(
        Context $context,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        parent::__construct($context);
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->apiKey = $this->scopeConfig->getValue(self::API_KEY_PATH);
        $this->city = $this->scopeConfig->getValue(self::CITY_DEFAULT_PATH);
    }

    private function getCacheKey($city)
    {
        return 'weather_data_' . strtolower($city);
    }

    private function getCachedWeatherData($city)
    {
        $cache = $this->cacheFrontendPool->get('default');
        return $cache->load($this->getCacheKey($city));
    }

    private function setCachedWeatherData($city, $data)
    {
        $cache = $this->cacheFrontendPool->get('default');
        $cache->save(
            serialize($data),
            $this->getCacheKey($city),
            ['weather_data'],
            self::CACHE_LIFETIME
        );
    }

    private function pullWeatherInfo($city) 
    {
        // First check cache
        $cachedData = $this->getCachedWeatherData($city);
        if ($cachedData) {
            $this->weatherInfo = unserialize($cachedData);
            return count($this->weatherInfo);
        }

        $cnt = 0;
        $this->weatherInfo = [];

        $apiUrl = "https://api.openweathermap.org/data/2.5/forecast/daily?q="
            . urlencode($city)
            . "&cnt=7&appid="
            . urlencode($this->apiKey)
            . "&units=metric&lang=vi";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if ($response === false) {
            curl_close($ch);
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);
        $data = json_decode($response, true);

        if (!isset($data['cod'])) return "Lỗi: No response code";
        if ($data['cod'] == 404) {
            return "Lỗi: Không tìm thấy tỉnh/thành phố này";
        }

        if ($data['cod'] == 200) {
            $forecasts = $data['list'];
            $cityName = $data['city']['name'];
            $country = $data['city']['country'];

            foreach ($forecasts as $day) {
                $this->weatherInfo[$cnt] = new WeatherInfo(
                    $cityName,
                    $country,
                    $day['weather'][0]['icon'],
                    $day['temp']['day'],
                    $day['weather'][0]['description'],
                    $day['humidity'],
                    $day['speed'],
                    $day['dt'],
                    $day['temp']['min'],
                    $day['temp']['max']
                );
                $cnt++;
            }

            // Cache the successful results
            if ($cnt == 7) {
                $this->setCachedWeatherData($city, $this->weatherInfo);
            }
        } else {
            return "Error: " . $data['message'];
        }

        return $cnt;
    }

    public function getDefaultCity() 
    {
        return $this->city;
    }

    public function getWeatherInfo($city = "hanoi") 
    {
        $res = $this->pullWeatherInfo($city);
        if ($res != 7) return $res;
        return $this->weatherInfo;
    }

    public function getApiKey() 
    {
        return $this->apiKey;
    }

    // Method to manually clear weather cache if needed
    public function clearWeatherCache($city = null)
    {
        $cache = $this->cacheFrontendPool->get('default');
        if ($city) {
            $cache->remove($this->getCacheKey($city));
        } else {
            $this->cacheTypeList->cleanType('default');
        }
    }
}