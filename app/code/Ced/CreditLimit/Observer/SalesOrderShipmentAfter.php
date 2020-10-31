<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CreditLimit
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CreditLimit\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderShipmentAfter
 * @package Ced\CreditLimit\Observer
 */
Class SalesOrderShipmentAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * 
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }
    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
    	
    	$shipment = $observer->getEvent()->getShipment();
    	/** @var \Magento\Sales\Model\Order $order */
    	$order = $shipment->getOrder();
    	if($order->getPayment()->getMethodInstance()->getCode()=="paybycredit"){
    		if(!$order->getCustomerIsGuest()){			
		    	$order->setState('pending_payment')->setStatus('shipped_pending_payment');
		    	$order->save();
    		}
    	}
    }
}    

