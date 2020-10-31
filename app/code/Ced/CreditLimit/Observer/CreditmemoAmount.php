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
 * Class CreditmemoAmount
 * @package Ced\CreditLimit\Observer
 */
class CreditmemoAmount implements ObserverInterface
{
	
	/**
	 * 
	 * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
	 * @param \Ced\CreditLimit\Helper\Data $helper
	 */
	
    public function __construct(
    	\Ced\CreditLimit\Model\CreditLimit $creditLimit,
    	\Ced\CreditLimit\Helper\Data $helper
    ) {
        $this->creditLimit = $creditLimit;
        $this->helper = $helper;
    }
    
    /**
     * 
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        $credtimemo = $observer->getEvent()->getCreditmemo();
        
        if($credtimemo->getOrder()->getPayment()->getMethodInstance()->getCode()=="paybycredit"){
            if(!$credtimemo->getOrder()->getCustomerIsGuest()){
	            $model = $this->creditLimit->load($credtimemo->getOrder()->getCustomerId(),'customer_id');
	            if($model->getId()){
	                $model->setRemainingAmount($model->getRemainingAmount()+$credtimemo->getBaseGrandTotal());                
	                if($this->helper->canPaymentDeduction()){
		                if($model->getPaymentDue()){
		                	$totalDue = $model->getPaymentDue() - $credtimemo->getBaseGrandTotal();
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
	             }
            }
        }
        return $this;
    }
}