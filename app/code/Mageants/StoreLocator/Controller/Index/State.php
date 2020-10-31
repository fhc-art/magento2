<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Index;
use Magento\Framework\View\Result\PageFactory;

/**
 * Store class
 */
class State extends \Magento\Framework\App\Action\Action
{
	public function __construct(
        \Magento\Framework\App\Action\Context      $context,
        \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        PageFactory $resultPageFactory
    ) {        
        $this->regionColFactory     = $regionColFactory;
        $this->resultJsonFactory            = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();

        $result                 = $this->resultJsonFactory->create();
        $regions=$this->regionColFactory->create()->getCollection()->addFieldToFilter('country_id',$this->getRequest()->getParam('country'));
        return $result->setData(['success' => true,'value'=>$regions->getData()]);

    }
}
