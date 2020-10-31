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

class ConfigurePlugin extends \Magento\Checkout\Controller\Cart\Configure
{
	/**
	 * 
	 * @param \Magento\Checkout\Controller\Cart\Configure $subject
	 * @param \Closure $proceed
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
    public function aroundExecute(\Magento\Checkout\Controller\Cart\Configure $subject, \Closure $proceed)
    { 	 	
        	$id = (int)$this->getRequest()->getParam('id');
        	$productId = (int)$this->getRequest()->getParam('product_id');
        	$product = $this->_objectManager->get('Magento\Catalog\Model\ProductRepository')->getById($productId);
        	if($product->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU){
        		$this->messageManager->addErrorMessage(__('We cannot configure this product.'));
        		return $this->_goBack();
        	}
    	return $proceed();
    }
}