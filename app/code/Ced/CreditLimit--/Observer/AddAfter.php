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
use Magento\Framework\Exception\LocalizedException;


Class AddAfter implements ObserverInterface
{
	
    /**
     * @param \Magento\Checkout\Model\Cart $cart
     */
	
    public function __construct(
    	\Magento\Checkout\Model\Cart $cart
    ) {
    	$this->cart = $cart;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
    	if($this->cart->getCreditLimitProduct())
    		return $this;
    	
    	$quote = $this->cart->getQuote();
    	$flag = false;
    	foreach($quote->getAllItems() as $item){
    		if($item->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU){
    			$flag = true;
    			break;
    		}
    	}
    	
    	if($flag){
    		throw new LocalizedException(__('Cannot Add Product to Cart if you have Credit Limit Pending Order in Cart'));
    	}
    }
}    

