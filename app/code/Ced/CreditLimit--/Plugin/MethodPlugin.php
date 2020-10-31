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
 * @category    Ced
 * @package     Ced_CreditLimit
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CreditLimit\Plugin;

class MethodPlugin
{
  /**
   * 
   * @param \Magento\Checkout\Model\Cart $cart
   * @param \Magento\Customer\Model\Session $session
   * @param \Ced\CreditLimit\Helper\Data $helper
   */
	public function __construct(
			\Magento\Checkout\Model\Cart $cart,
			\Magento\Customer\Model\Session $session,
			\Ced\CreditLimit\Helper\Data $helper
				
	){
		$this->helper = $helper;
		$this->cart = $cart;
		$this->session = $session;
	}
    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Payment\Model\MethodInterface[]
     * @api
     */
    public function aftergetAvailableMethods($subject, $result)
    { 
    	
        if($this->session->isLoggedIn()){
        	
        	$creditdata = $this->helper->getCustomerCreditLimit($this->session->getCustomerId());
        	$isOfflineHide = $this->helper->getConfigValue('b2bextension/credit_limit/hide_offline');
        	$flag = false;
        	$discountTotal = 0;
        	
        	foreach ($this->cart->getQuote()->getAllItems() as $item){
        		if($item->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU){
        			$flag = true;
        			break;
        		}
        		$discountTotal += $item->getDiscountAmount();
        	}
        	     	
        	$total = $this->cart->getQuote()->getBaseGrandTotal();
        	
        	$paymentamount = $total-$discountTotal;
        	if($creditdata->getRemainingAmount() < $paymentamount || $flag){
        		foreach($result as $key => $payment){
        			
        			if($payment->getCode() == 'paybycredit'){
        				unset($result[$key]);
        			}
        			
        			 if($flag && $isOfflineHide){
        				if($payment->IsOffline()){
        					unset($result[$key]);
        				}
        			} 
        		}
        	}
        	return $result;
        }else{
        	foreach($result as $key => $payment){
        		if($payment->getCode() == 'paybycredit'){
        			unset($result[$key]);
        		}
        	}
        	return $result;
        }
       
    }
}