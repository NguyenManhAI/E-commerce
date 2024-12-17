<?php
namespace Bang\Weather\Helper;

use Bang\Weather\Model\WeatherInfo;
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    const API_KEY_PATH = "api/config/key";
    const CITY_DEFAULT_PATH = "api/city/city1";

    protected $apiKey;
    protected $city;
    protected $weatherInfo;

    public function __construct(\Magento\Framework\App\Helper\Context $context) {
        parent::__construct($context);
        $this->apiKey = $this->scopeConfig->getValue(self::API_KEY_PATH);
        $this->city = $this->scopeConfig->getValue(self::CITY_DEFAULT_PATH);
    }

    private function pullWeatherInfo($city) {
        $cnt = 0;
        $this->weatherInfo = [];
        //$apiUrl = "https://api.openweathermap.org/data/2.5/forecast/daily?q=" ."hanoi" . "&cnt=7&appid={$this->apiKey}&units=metric";
        //. urlencode($this->city)
        /*
            "https://api.openweathermap.org/data/2.5/forecast/daily?q=". urlencode($this->city). "&cnt=6&appid=" . 
                urlencode($this->apiKey) . "&units=metric&lang=vi";
        */

        $apiUrl = "https://api.openweathermap.org/data/2.5/forecast/daily?q=".urlencode($city)."&cnt=7&appid=".urlencode($this->apiKey)."&units=metric&lang=vi";;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        if($response === false) {
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
                $date = $day['dt'];
                $temperature = $day['temp']['day']; 
                $humidity = $day['humidity']; 
                $description = $day['weather'][0]['description']; 
                $windSpeed = $day['speed']; 
                $tempMin = $day['temp']['min']; 
                $tempMax = $day['temp']['max']; 
                $icon = $day['weather'][0]['icon']; 
                $this->weatherInfo[$cnt] = new WeatherInfo($cityName, $country, $icon, $temperature, 
                                        $description, $humidity, $windSpeed, $date, 
                                        $tempMin, $tempMax);
                $cnt++;
            }
        } else {
            echo "Error: " . $data['message'] . "\n";
        }
        return $cnt;
    }

    public function getDefaultCity() {
        return $this->city;
    }

    public function getWeatherInfo($city = "hanoi") {
        $res = $this->pullWeatherInfo($city);
        if ($res != 7) return $res;
        return $this->weatherInfo;
    }

    public function getApiKey() {
        return $this->apiKey;
    }
}