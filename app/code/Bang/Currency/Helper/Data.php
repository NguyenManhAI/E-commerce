<?php
namespace Bang\Currency\Helper;

use Exception;
use Bang\Currency\Model\CurrencyRate;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Data extends AbstractHelper 
{
    private const URL_SOURCE = "https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx";
    private const CACHE_KEY = 'vietcombank_exchange_rates';
    private const CACHE_LIFETIME = 300; 

    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(
        Context $context,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    private function fetchXmlData()
    {
        $ch = curl_init();
        $headr = array();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_URL, self::URL_SOURCE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'spider');
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Error fetching data: ' . curl_error($ch));
        }

        return $response;
    }

    private function getCachedData()
    {
        $cache = $this->cacheFrontendPool->get('default');
        return $cache->load(self::CACHE_KEY);
    }

    private function setCachedData($data)
    {
        $cache = $this->cacheFrontendPool->get('default');
        $cache->save(
            $data,
            self::CACHE_KEY,
            ['currency_rates'],
            self::CACHE_LIFETIME
        );
    }

    public function getExchangeRates()
    {
        try {
            // Try to get data from cache first
            $cachedData = $this->getCachedData();
            if ($cachedData) {
                return unserialize($cachedData);
            }

            // If no cached data, fetch from API
            $xmlData = $this->fetchXmlData();
            
            // Parse XML data
            $xmlLoader = simplexml_load_string($xmlData);
            if ($xmlLoader === false) {
                throw new Exception('Error parsing XML.');
            }

            $rates = [];
            foreach ($xmlLoader->Exrate as $exrate) {
                $currencyCode = (string)$exrate['CurrencyCode'];
                $currencyName = (string)$exrate['CurrencyName'];
                $buy = (string)$exrate['Buy'];
                $transfer = (string)$exrate['Transfer'];
                $sell = (string)$exrate['Sell'];
                $newRate = new CurrencyRate($currencyCode, $currencyName, $buy, $transfer, $sell);
                $rates[] = $newRate;
            }

            // Cache the results
            $this->setCachedData(serialize($rates));

            return $rates;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function sayHello()
    {
        return "currency helper hello";
    }
}