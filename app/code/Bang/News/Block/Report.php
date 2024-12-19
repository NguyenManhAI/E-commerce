<?php
namespace Bang\News\Block;

class Report extends \Magento\Framework\View\Element\Template {
    protected $helper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bang\News\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getNews($page = 1)
    {
        return $this->helper->getNews($page);
    }
}