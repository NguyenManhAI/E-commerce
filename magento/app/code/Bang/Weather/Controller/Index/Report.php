<?php
namespace Bang\Weather\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Bang\Weather\Helper\Data;

class Report extends Action
{
	protected $resultJsonFactory;
    protected $formKeyValidator;
    protected $helper;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Validator $formKeyValidator,
        Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $result->setData([
                'error' => true,
                'message' => 'Invalid Form Key'
            ]);
        }

        $city = $this->getRequest()->getParam('city');
        if (!$city) {
            return $result->setData([
                'error' => true,
                'message' => 'City parameter is required'
            ]);
        }

        $apiKey = $this->helper->getApiKey();
        $url = "https://api.openweathermap.org/data/2.5/forecast/daily?q=" . 
               urlencode($city) . "&cnt=7&appid=" . urlencode($apiKey) . 
               "&units=metric&lang=vi";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        
        if ($response === false) {
            return $result->setData([
                'error' => true,
                'message' => 'Error fetching weather data: ' . curl_error($ch)
            ]);
        }
        
        curl_close($ch);
        $weatherData = json_decode($response, true);

        if (isset($weatherData['cod']) && $weatherData['cod'] === '404') {
            return $result->setData([
                'error' => true,
                'message' => 'Không tìm thấy thành phố này. Vui lòng kiểm tra lại tên thành phố.'
            ]);
        }

        return $result->setData($weatherData);
    }
}