<?php
namespace Company\Module\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;

class Calculate extends Action
{
    protected $resultJsonFactory;
    protected $formKeyValidator;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Validator $formKeyValidator
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        // Kiểm tra form key
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $result->setData([
                'error' => true,
                'message' => 'Invalid Form Key'
            ]);
        }
    
        $number1 = $this->getRequest()->getParam('number1');
        $number2 = $this->getRequest()->getParam('number2');

        return $result->setData([
            'total' => $number1 + $number2
        ]);
    }
}