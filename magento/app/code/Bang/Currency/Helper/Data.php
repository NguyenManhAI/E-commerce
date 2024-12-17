<?php

namespace Bang\Currency\Helper;

use Exception;
use Bang\Currency\Model\CurrencyRate;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    private const URL_SOURCE = "https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx";
    private function fetchXmlData()
    {
        // Sử dụng cURL để lấy dữ liệu
        $ch = curl_init();
        $headr = array();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_URL, self::URL_SOURCE ); // get the url contents
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'spider');
        $response = curl_exec($ch);
        curl_close($ch);
        // Kiểm tra lỗi cURL
        if ($response === false) {
            throw new Exception('Error fetching data: ' . curl_error($ch));
        }
        
        
        
        return $response;
    }

    public function getExchangeRates()
    {
        try {
            // Lấy dữ liệu XML
            $xmlData = $this->fetchXmlData();

            
            // Tạo đối tượng SimpleXML để phân tích dữ liệu
            $xmlLoader = simplexml_load_string($xmlData);
            if ($xmlLoader === false) {
                throw new Exception('Error parsing XML.');
            }
            $rates = [];
            foreach($xmlLoader -> Exrate as $exrate) {
                $currencyCode = (string) $exrate['CurrencyCode'];
                $currencyName = (string) $exrate['CurrencyName'];
                $buy = (string) $exrate['Buy'];
                $transfer = (string) $exrate['Transfer'];
                $sell = (string) $exrate['Sell'];
                $newRate = new CurrencyRate($currencyCode, $currencyName, $buy, $transfer, $sell);
                $rates[] = $newRate;
            }

            return $rates;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function sayHello() {
        return "currency helper hello";
    }
}