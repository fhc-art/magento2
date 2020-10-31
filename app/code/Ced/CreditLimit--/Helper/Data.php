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
 
namespace Ced\CreditLimit\Helper;
 use Magento\Framework\App\Helper;

 /**
  * Class Data
  * @package Ced\CreditLimit\Helper
  */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   /**
    * 
    * @param Helper\Context $context
    * @param \Magento\Customer\Model\Session $session
    * @param \Magento\Customer\Model\CustomerFactory $customerFactory
    * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
    * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
    */
	public function __construct(
			Helper\Context $context,
			\Magento\Customer\Model\Session $session,
			\Magento\Customer\Model\CustomerFactory $customerFactory,
			\Magento\Framework\Pricing\Helper\Data $pricingHelper,
			\Ced\CreditLimit\Model\CreditLimit $creditLimit
	) 
	{
		$this->session = $session;
		$this->customer = $customerFactory;
		$this->pricingHelper = $pricingHelper;
		$this->creditLimit = $creditLimit;
		parent::__construct($context);
	}

    /**
     * @param $price
     * @return mixed
     */
	public function getFormattedPrice($price){
		$formattedPrice = $this->pricingHelper->currency($price, true, false);
		return $formattedPrice;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function checkLimitAssign(){
		
		$assignType = $this->getAssignType();
		$flag = false;
		if($assignType=='group'){
			$groupLimit = json_decode($this->getConfigValue('b2bextension/credit_limit/group_limit'),true);
			
			if(is_array($groupLimit) && count($groupLimit)>0){			
				foreach($groupLimit as $limitValue){
					if($limitValue['customer_group_id'] == $this->session->getCustomer()->getGroupId()){
						$flag = true;
						break;
					}
				}
			}	
		}
	
		$creditLimit = $this->getCustomerCreditLimit($this->session->getCustomerId());
		
		if($creditLimit->getId() || $flag)
			return true;
		
			return false;
	}
	
	/**
	 * 
	 * @return \Magento\Framework\App\Config\mixed
	 */
	public function getAssignType(){
		
		return  $this->getConfigValue('b2bextension/credit_limit/assign_type');
	}
	/**
	 * 
	 * @param unknown $customerId
	 * @return unknown
	 */
	public function getCustomerCreditLimit($customerId){
		
		$creditLimit =  $this->creditLimit->load($customerId,'customer_id');
		
		return $creditLimit;
	}
	/**
	 * 
	 * @return boolean
	 */
	public function isButtonEnabled(){
		
		if($this->getAssignType()=='group')
			return false;
		
		return true;
		
	}
	
	/**
	 * 
	 * @param string $path
	 * @return \Magento\Framework\App\Config\mixed
	 */
	public function getConfigValue($path){
		
		return  $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function canPaymentDeduction(){
		$status = $this->getConfigValue('payment/paybycredit/order_status');
		if($status=='pending' || $status=='ced_credit_limit')
			return true;
		return false;
	}
}