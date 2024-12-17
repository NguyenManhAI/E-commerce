<?php

namespace Bang\Test\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Increment extends Action
{
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $action = $this->getRequest()->getParam('action');
        $count = (int)$this->getRequest()->getParam('count', 0);

        switch ($action) {
            case 'increment':
                $count++;
                break;
            case 'decrement':
                $count--;
                break;
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData(['count' => $count]);
    }
}
