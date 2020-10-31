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

Class RefundAmount implements ObserverInterface
{
	
    /**
     * 
     * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
     * @param \Ced\CreditLimit\Model\Transaction $transaction
     */
    public function __construct(
    	\Ced\CreditLimit\Model\CreditLimit $creditLimit,
    	\Ced\CreditLimit\Model\Transaction $transaction
    ) {
        $this->creditLimit = $creditLimit;
        $this->transaction = $transaction;
    }
    
    /**
     * Order credit limit update
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        $orderData = $observer->getEvent()->getOrder();
        if($orderData->getPayment()->getMethodInstance()->getCode()=="paybycredit"){
            if(!$orderData->getCustomerIsGuest()){
            	$model = $this->creditLimit->load($orderData->getCustomerId(),'customer_id');
	            if($model->getId()){
	               $model->setRemainingAmount($model->getRemainingAmount()+$orderData->getBaseGrandTotal());
	               if($model->getPaymentDue()){
	               	$totalDue = $model->getPaymentDue() - $orderData->getBaseGrandTotal();
	               	if($totalDue>=0){
	               		$model->setPaymentDue($totalDue);
	               	}else{
	               		$model->setPaymentDue(0.00);
	               	}
	               }else{
	               	$model->setPaymentDue($orderData->getBaseGrandTotal());
	               }
	               $model->save();
	               $this->applyTransaction($orderData);
	             }
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param  $orderData
     */
    protected function applyTransaction($orderData){
    	$transaction = $this->transaction;
    	$transaction->setCustomerId($orderData->getCustomerId());
    	$transaction->setAmountPaid($orderData->getGrandTotal());
    	$transaction->setTransactionId($orderData->getIncrementId());
    	$transaction->setCreatedAt(time());
    	$transaction->save();
    }   
}