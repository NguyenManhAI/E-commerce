<?php
namespace Bang\News\Controller\Index;

use Bang\News\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		parent::__construct($context);
	}

	public function execute(){
		return $this->_pageFactory->create();
	}
}