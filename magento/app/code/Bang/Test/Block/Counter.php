<?php

namespace Bang\Test\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Session\SessionManager;

class Counter extends Template
{
    protected $session;

    public function __construct(
        Template\Context $context,
        SessionManager $session,
        array $data = []
    ) {
        $this->session = $session;
        parent::__construct($context, $data);
    }

    public function getCounterValue()
    {
        return $this->session->getCounter() ?? 0;
    }
}
