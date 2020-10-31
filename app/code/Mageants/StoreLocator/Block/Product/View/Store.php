<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block\Product\View;
use Magento\Catalog\Block\Product\AbstractProduct;

/**
 * Store In product View
 */
class Store extends AbstractProduct
{
	/**
	 * check for product available in store
	 *
	 * @return storeCollection
	 */
    public function CheckProductAvailable($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('\Mageants\StoreLocator\Model\StoreProduct')->getCollection()
                    ->addFieldToFilter("product_id", $id);
        $store_id=array();
        foreach($model as $store)
        {
            $store_id[]=$store['store_id'];
        }
        $collection=$objectManager->create('\Mageants\StoreLocator\Model\ManageStore')->getCollection()
                    ->addFieldToFilter("sstatus","Enabled")
                    ->addFieldToFilter('store_id', ['in' => $store_id]);
        return $collection;
    }

	/**
	 * Prepare for store Mark template
	 *
	 * @return $this
	 */
    public function getStoreMarkerTemplate()
    {
        return $this->_scopeConfig->getValue('StoreLocator/general/mark_template');
    }

    /**
	 * Get Api key for Google Map
	 *
	 * @return $this
	 */
    public function getApiKey()
    {
        return $this->_scopeConfig->getValue('StoreLocator/map/map_key');
    }

    /**
     * Get store Marker for google map
     *
     * @return $this
     */
    public function getProductText()
    {
        return $this->_scopeConfig->getValue('StoreLocator/general/pplabel');
    }    
}
