<?php
namespace Company\Module\Block;

use Magento\Framework\View\Element\Template;

class Calculator extends Template
{
    public function getCalculateActionUrl()
    {
        return $this->getUrl('company_module/index/calculate');
    }
}