<?php
namespace Bang\Currency\Controller\Index;
use Magento\Framework\Http\Client\Curl;

class Test extends \Magento\Framework\App\Action\Action
{
    protected $curl;
    const API_URL = "https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx";
    public function __construct(\Magento\Framework\App\Action\Context $context, Curl $curl) {
        parent::__construct($context);
        $this->curl = $curl;
        
        
    }
	public function execute(){
		try {
            $this->curl->get(self::API_URL);
            $response = $this->curl->getBody();
            
            // Chuyển dữ liệu XML thành mảng
            $xml = simplexml_load_string($response);
            $json = json_encode($xml);
            $data = json_decode($json, true);

            // Bạn có thể thay đổi tùy theo cấu trúc XML của API
            echo $data;
        } catch (\Exception $e) {
            echo "err";
            return false;
        }
    }
}