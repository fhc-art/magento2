<?php
/**
 * Copyright © 2015 Inchoo d.o.o.
 * created by Zoran Salamun(zoran.salamun@inchoo.net)
 */
namespace Mageants\StoreLocator\Controller\Store;
use Magento\Framework\View\Result\PageFactory;
class Store extends \Magento\Framework\App\Action\Action
{
    /**
	 * Result PageFactory
	 *
	 * @var Magento\Framework\View\Result\PageFactory
	 */
    protected $_resultPageFactory;
	
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param Magento\Framework\View\Result\PageFactory
	 */
    public function __construct
    (PageFactory $resultPageFactory,\Magento\Framework\App\Action\Context $context)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);          
    }
	
	/**
	 * perform execute method for Index
	 *
	 * @return $void
	 */
    public function execute()
    {
    	$resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('storelocator_store_store');
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}