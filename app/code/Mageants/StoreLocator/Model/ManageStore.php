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
class ManageStore extends AbstractModel
{

	/**
	 * init store
	 */
    protected function _construct()
    {
        $this->_init('Mageants\StoreLocator\Model\ResourceModel\ManageStore');
    }
    
	/**
	 * fetch product for store
	 *
	 * @param \Mageants\StoreLocator\Model\ManageStore $object
	 * @return $string
	 */
    public function getProducts(\Mageants\StoreLocator\Model\ManageStore $object)
    {
        $tbl = $this->getResource()->getTable(\Mageants\StoreLocator\Model\ResourceModel\ManageStore::TBL_ATT_PRODUCT);
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
        ->where(
            'store_id = ?',
            (int)$object->getId()
        );
        return $this->getResource()->getConnection()->fetchCol($select);
    }
}
