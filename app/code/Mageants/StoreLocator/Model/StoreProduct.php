<?php 
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Model;
use Magento\Framework\Model\AbstractModel;

/**
 * Locator Address tab
 */
class StoreProduct extends AbstractModel
{
	/**
	 * init store product
	 */
    protected function _construct()
    {
        $this->_init('Mageants\StoreLocator\Model\ResourceModel\StoreProduct');
    }
}
