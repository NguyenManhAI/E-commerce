<?php
namespace Bang\Weather\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Bang\Weather\Helper\Data;

class Report extends Template
{
    protected $helper;
    
    public function __construct(
        Context $context,
        Data $helper,
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function getApiKey()
    {
        return $this->helper->getApiKey();
    }

    public function getReportActionUrl()
    {
        return $this->getUrl('weather/index/report');
    }

    public function getWeatherData($city)
    {
        return $this->helper->getWeatherInfo($city);
    }
}
