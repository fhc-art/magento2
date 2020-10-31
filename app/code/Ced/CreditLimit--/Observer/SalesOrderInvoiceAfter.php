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
Class SalesOrderInvoiceAfter implements ObserverInterface
{
   
    /**
     * 
     * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
     * @param \Ced\CreditLimit\Model\Transaction $transaction
     * @param \Ced\CreditLimit\Helper\Data $helper
     */
    public function __construct(
    	\Ced\CreditLimit\Model\CreditLimit $creditLimit,
    	\Ced\CreditLimit\Model\Transaction $transaction,
    	\Ced\CreditLimit\Helper\Data $helper
    ) {
        $this->creditLimit = $creditLimit;
        $this->transaction = $transaction;
        $this->helper = $helper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
    	$invoice = $observer->getEvent()->getInvoice();
    	$orderData = $invoice->getOrder();
    	if($orderData->getPayment()->getMethodInstance()->getCode()=="paybycredit"){
    		if(!$orderData->getCustomerIsGuest()){	
    			$model = $this->creditLimit->load($orderData->getCustomerId(),'customer_id');
	    		if($model->getId()){
	    		    if($this->helper->canPaymentDeduction()){
	    			    $model->setRemainingAmount($model->getRemainingAmount()+$invoice->getBaseGrandTotal());
		    			if($model->getPaymentDue()){
		    				$totalDue = $model->getPaymentDue() - $invoice->getBaseGrandTotal();
		    				if($totalDue>=0){
		    					$model->setPaymentDue($totalDue);
		    				}else{
		    					$model->setPaymentDue(0.00);
		    				}
		    			}else{
		    				$model->setPaymentDue(0.00);
		    			}
	    			}
	    			$model->save();
	    			$this->applyTransaction($invoice);
	    		}
    		}
    	}
    	return $this;
    }
    
    /**
     * 
     * save transaction for Order
     */
    protected function applyTransaction($invoice){
    	$transaction = $this->transaction;
    	$transaction->setCustomerId($invoice->getOrder()->getCustomerId());
    	$transaction->setAmountPaid($invoice->getBaseGrandTotal());
    	$transaction->setTransactionId($invoice->getIncrementId());
    	$transaction->setCreatedAt(time());
    	$transaction->save();
    }
}    

