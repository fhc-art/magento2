<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Storelocator\Edit\Tabs;

use Mageants\StoreLocator\Model\ManageStoreFactory; 
/**
 * Locator Product tab
 */
class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
	/**
	 * Current productCollectionFactory
	 *
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
    protected $productCollectionFactory;
	
	/**
	 * Manage Store Factory
	 *
	 * @var \Mageants\StoreLocator\Model\ManageStoreFactory
	 */
    protected $managestoreFactory;
	
	/**
	 * Manage Registry
	 *
	 * @var \Magento\Framework\Registry
	 */
    protected $registry;
	
	/**
	 * Declare Object Manager
	 *
	 * @var Null
	 */
    protected $_objectManager = null;
	
	/**
	 * Product Model Type
	 *
	 * @var \Magento\Catalog\Model\Product\Type
	 */
    protected $_type;
	
	/**
	 * product attribute status
	 *
	 * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
	 */
    protected $_status;
	
	/**
	 * Model product Visibility
	 *
	 * @var \Magento\Catalog\Model\Product\Visibility
	 */
    protected $_visibility;
	
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Magento\Backend\Helper\Data
	 * @param \Magento\Framework\Registry
	 * @param \Magento\Framework\ObjectManagerInterface
	 * @param \Mageants\StoreLocator\Model\ManageStoreFactory
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 * @param \Magento\Catalog\Model\Product\Type
	 * @param \Magento\Catalog\Model\Product\Attribute\Source\Status
	 * @param \Magento\Catalog\Model\Product\Visibility
	 * @param array
	 */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ManageStoreFactory $managestoreFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        array $data = []
    ) 
    {
        $this->managestoreFactory = $managestoreFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        parent::__construct($context, $backendHelper, $data);
    }
	
	/**
	 * Prepare construct
	 *
	 */ 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
    }
	
	/**
	 * Prepare Column to Filter
	 *
	 * @return $this
	 */ 
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

	/**
	 * Prepare Collection
	 *
	 * @return _parentCollection
	 */ 
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        
        $collection->joinField(
            'qty', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $model = $this->_objectManager->get('\Mageants\StoreLocator\Model\StoreProduct');
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '10px',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );
        
        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->_visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->_type->getOptionArray(),
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'width' => '50px',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('storelocator/storelocator/productsgrid', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedProducts()
    {
        $allstore = $this->getAllStore();
        return $allstore->getProducts($allstore);
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $allstore = $this->getAllStore();
        $selected = $allstore->getProducts($allstore);
        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected; 
    }

    protected function getAllStore()
    {
        $allstoreId = $this->getRequest()->getParam('store_id');
        $allstore   = $this->managestoreFactory->create();
        if ($allstoreId) {
            $allstore->load($allstoreId);
        }
        return $allstore;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
