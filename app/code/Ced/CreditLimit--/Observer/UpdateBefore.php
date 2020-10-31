<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
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
use Magento\Framework\Exception\LocalizedException;


Class UpdateBefore implements ObserverInterface
{
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$info = $observer->getEvent()->getInfo();
    	$cart  = $observer->getEvent()->getCart();
    	$flag = false;
    	
    	foreach ($info->getData() as $itemId => $itemInfo) {
    		
    		$item = $cart->getQuote()->getItemById($itemId);
    		if (!$item) {
    			continue;
    		}
    		if($item->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU){
    			$flag = true;
    			break;
    		}
    	}
    	
    	if($flag){
    		throw new LocalizedException(__('Cannot Update This Item'));
    	}
    	
    }
}    

