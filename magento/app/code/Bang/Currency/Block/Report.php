<?php
namespace Bang\Currency\Block;

class Report extends \Magento\Framework\View\Element\Template 
{
    protected $helper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bang\Currency\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getExchangeRates()
    {
        return $this->helper->getExchangeRates();
    }
}