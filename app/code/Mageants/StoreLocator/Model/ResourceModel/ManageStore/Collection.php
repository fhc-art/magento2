<?php 
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Model\ResourceModel\ManageStore;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * collection for store
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'store_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageants\StoreLocator\Model\ManageStore', 'Mageants\StoreLocator\Model\ResourceModel\ManageStore');
    }
}
