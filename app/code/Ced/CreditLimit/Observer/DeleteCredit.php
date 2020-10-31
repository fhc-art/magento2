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
class DeleteCredit implements ObserverInterface
{
    /**
     * @var \Ced\CreditLimit\Model\CreditLimit
     */
    protected $_creditLimit;
    public function __construct(
    	\Ced\CreditLimit\Model\CreditLimit $creditLimit
    ) {
        $this->_creditLimit = $creditLimit;
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
            $creditLimit =  $this->_creditLimit->load($customerId,'customer_id');
            if($creditLimit && $creditLimit->getId()) {
                $creditLimit->delete();                
            }
        }
    }
}