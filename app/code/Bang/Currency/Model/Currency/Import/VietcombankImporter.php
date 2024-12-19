<?php
namespace Bang\Currency\Model\Currency\Import;

use Magento\Directory\Model\Currency\Import\AbstractImport;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Xml\Parser;
use Magento\Directory\Model\CurrencyFactory;
use Bang\Currency\Helper\Data;

class VietcombankImporter extends AbstractImport
{
    const CURRENCY_CONVERTER_URL = 'https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx';
    
    protected $curl;
    protected $xmlParser;
    protected $currencyFactory;
    protected $cache;
    protected $helper;
    protected $cacheKey = 'vietcombank_currency_rates';
    protected $cacheTags = ['currency', 'vietcombank'];
    protected $cacheLifetime = 3600; // 1 hour

    public function __construct(
        CurrencyFactory $currencyFactory,
        Curl $curl,
        Parser $xmlParser,
        \Magento\Framework\App\CacheInterface $cache,
        Data $helper
    ) {
        $this->curl = $curl;
        $this->xmlParser = $xmlParser;
        $this->cache = $cache;
        $this->helper = $helper;
        parent::__construct($currencyFactory);
    }

    /**
     * @inheritdoc
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
        try {
            // Try to get rates from cache first
            $rates = $this->getRatesFromCache();
            
            if (!$rates) {
                $rates = $this->helper->getExchangeRates();                
                // Save to cache
                $this->saveRatesToCache($rates);
            }
            
            // Xây dựng mảng tỉ giá chéo
            $crossRates = [];
            foreach ($rates as $rate) {
                // Lấy tỉ giá mua (Buy rate)
                $buyRate = str_replace(',', '', $rate->getSell());
                $currencyCode = $rate->getCode();
                
                // Tỉ giá từ ngoại tệ sang VND
                $crossRates['VND'][$currencyCode] = (float)$buyRate;
                // Tỉ giá từ VND sang ngoại tệ
                if ((float) $buyRate != 0) {
                    $crossRates[$currencyCode]['VND'] = 1 / (float)$buyRate;
                } else {
                    $crossRates[$currencyCode]['VND'] = "-";
                }
                
                // Tính tỉ giá chéo giữa các ngoại tệ
                foreach ($rates as $otherRate) {
                    $otherCode = $otherRate->getCode();
                    if ($currencyCode !== $otherCode) {
                        $otherBuyRate = str_replace(',', '', $otherRate->getBuy());
                        if ((float) $otherBuyRate != 0) {
                            $crossRates[$otherCode][$currencyCode] = (float)$buyRate / (float)$otherBuyRate;
                        } else {
                            $crossRates[$otherCode][$currencyCode] = "-";
                        }
                    }
                }
                
                // Tỉ giá với chính nó là 1
                $crossRates[$currencyCode][$currencyCode] = 1;
            }
            
            // Thêm tỉ giá VND với chính nó
            $crossRates['VND']['VND'] = 1;

            // Trả về tỉ giá theo cặp tiền tệ yêu cầu
            if (isset($crossRates[$currencyFrom][$currencyTo])) {
                return $crossRates[$currencyFrom][$currencyTo];
            }
            
            throw new \Exception(sprintf('Cannot retrieve rate from "%s" to "%s".', $currencyFrom, $currencyTo));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function getRatesFromCache()
    {
        $cachedData = $this->cache->load($this->cacheKey);
        if (!$cachedData) {
            return false;
        }
        
        // Chuyển đổi dữ liệu cache thành đối tượng
        $rates = [];
        $decodedData = json_decode($cachedData, true);
        foreach ($decodedData as $rateData) {
            $rate = new \Magento\Framework\DataObject($rateData);
            $rates[] = $rate;
        }
        return $rates;
    }

    protected function saveRatesToCache($rates)
    {
        // Chuyển đổi các đối tượng thành array trước khi cache
        $ratesArray = array_map(function($rate) {
            return [
                'code' => $rate->getCode(),
                'buy' => $rate->getBuy(),
                'sell' => $rate->getSell(),
                'transfer' => $rate->getTransfer(),
                'name' => $rate->getName()
            ];
        }, $rates);

        $this->cache->save(
            json_encode($ratesArray),
            $this->cacheKey,
            $this->cacheTags,
            $this->cacheLifetime
        );
    }

    // public function fetchRates()
    // {
    //     $data = [];
    //     $currencies = $this->_getCurrencyCodes();
    //     $defaultCurrencies = $this->_getDefaultCurrencyCodes();
    //     set_time_limit(0);
    //     foreach ($defaultCurrencies as $currencyFrom) {
    //         if (!isset($data[$currencyFrom])) {
    //             $data[$currencyFrom] = [];
    //         }

    //         foreach ($currencies as $currencyTo) {
    //             if ($currencyFrom == $currencyTo) {
    //                 $data[$currencyFrom][$currencyTo] = $this->_numberFormat(1);
    //             } else {
    //                 $data[$currencyFrom][$currencyTo] = $this->_numberFormat(
    //                     $this->_convert($currencyFrom, $currencyTo)
    //                 );
    //             }
    //         }
    //         ksort($data[$currencyFrom]);
    //     }
    //     ini_restore('max_execution_time');

    //     return $data;
    // }
}
