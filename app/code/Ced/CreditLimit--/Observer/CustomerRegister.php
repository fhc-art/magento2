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
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CreditLimit\Observer; 
use Magento\Framework\Event\ObserverInterface;
class CustomerRegister implements ObserverInterface
{
	/**
	 *
	 * @param \Ced\CreditLimit\Helper\Data $helper
	 */
	
	public function __construct(
			\Ced\CreditLimit\Helper\Data $helper
	) {
		$this->helper = $helper;
	}
 
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerData = $observer->getEvent()->getCustomer();
        $customerId = $customerData->getId();
        if($customerId) {
            if($this->helper->getAssignType()=='group'){
            	$groupLimit = json_decode($this->helper->getConfigValue('b2bextension/credit_limit/group_limit'),true);
            	if(is_array($groupLimit) && count($groupLimit)>0){
	            	foreach($groupLimit as $customervalue){    			
	            		if ($customervalue['customer_group_id'] == $customerData->getGroupId())
	            		{
	            			$creditLimit = $this->helper->getCustomerCreditLimit($customerId);
	            			if(!$creditLimit->getId()){
	            					$creditLimit->setCreditAmount($customervalue['credit_limit'])
	            					->setRemainingAmount($customervalue['credit_limit'])
	            					->setUsedAmount(0.00)
	            					->setCustomerId($customerId)
	            					->setCustomerEmail($customerData->getEmail())
	            					->save();
	            			        break;
	            			}
	            		}
	            	}
            	}	
            }
        }
    }
}