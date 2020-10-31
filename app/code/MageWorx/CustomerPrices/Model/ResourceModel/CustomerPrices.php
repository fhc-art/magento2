<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Model\ResourceModel;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use MageWorx\CustomerPrices\Helper\Base as HelperBase;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use MageWorx\CustomerPrices\Model\CustomerPrices as CustomerPricesModel;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices\Collection as CustomerPricesCollection;

class CustomerPrices extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var HelperBase
     */
    protected $helperBase;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|false
     */
    private $connection;

    /**
     * @var string
     */
    protected $linkField;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var HelperCalculate
     */
    protected $helperCalculate;

    /**
     * CustomerPrices constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param HelperCalculate $helperCalculate
     * @param HelperBase $helperBase
     * @param StoreManager $storeManager
     * @param Config $eavConfig
     */
    public function __construct(
        Context $context,
        DateTime $date,
        HelperCalculate $helperCalculate,
        HelperBase $helperBase,
        StoreManager $storeManager,
        Config $eavConfig
    ) {
        $this->date            = $date;
        $this->helperCalculate = $helperCalculate;
        $this->helperBase      = $helperBase;
        $this->storeManager    = $storeManager;
        $this->eavConfig       = $eavConfig;
        parent::__construct($context);

        $this->connection = $this->getConnection();
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_customerprices', 'entity_id');
        $this->date = date('Y-m-d H:i:s', time());
    }

    /**
     * Get all data from table mageworx_customerprices
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadCustomerPricesCollection()
    {
        $select = $this->connection->select()
                                   ->from(['customerprices' => $this->getMainTable()]);
        $data   = $this->connection->fetchAll($select);

        return $data;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteNotCorrectSpecialPrice()
    {
        $tableName               = $this->getTable('catalog_product_entity_decimal');
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();

        $this->connection->delete(
            $tableName,
            [
                'attribute_id = ?' => $specialPriceAttributeId,
                'store_id = ?'     => '1',
                'value IS NULL'
            ]
        );
    }

    /**
     * @param int $productId
     * @param int $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteProductCustomerPrice($productId, $customerId)
    {
        $tableName = $this->getMainTable();
        $this->connection->delete(
            $tableName,
            [
                'product_id' . ' = ?'  => $productId,
                'customer_id' . ' = ?' => $customerId
            ]
        );
    }

    /**
     * @param int $customerId
     * @param int $typePrice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteProductsFromTableMageworxCustomerGroupPrices($productIds, $customerId, $typePrice)
    {
        $tableName = $this->getMainTable();
        $this->connection->delete(
            $tableName,
            [
                'customer_id = ?'    => $customerId,
                'attribute_type = ?' => $typePrice,
                'product_id IN (?)'  => $productIds,
            ]
        );
    }

    /**
     * Save product group price
     *
     * @param $attributeType
     * @param $attributeId
     * @param $productId
     * @param $price
     * @param $priceType
     * @param $specialPrice
     * @param $specialPriceType
     * @param $discount
     * @param $discountPriceType
     * @param $priceSign
     * @param $priceValue
     * @param $specialPriceSign
     * @param $specialPriceValue
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveProductCustomerPrice(
        $attributeType,
        $customerId,
        $productId,
        $price,
        $priceType,
        $specialPrice,
        $specialPriceType,
        $discount,
        $discountPriceType,
        $priceSign,
        $priceValue,
        $specialPriceSign,
        $specialPriceValue
    ) {
        $tableName = $this->getMainTable();

        $data = [
            'attribute_type'      => $attributeType,
            'customer_id'         => $customerId,
            'product_id'          => $productId,
            'price'               => $price,
            'price_type'          => $priceType,
            'special_price'       => $specialPrice,
            'special_price_type'  => $specialPriceType,
            'discount'            => $discount,
            'discount_price_type' => $discountPriceType,
            'price_sign'          => $priceSign,
            'price_value'         => $priceValue,
            'special_price_sign'  => $specialPriceSign,
            'special_price_value' => $specialPriceValue
        ];

        $this->connection->insert($tableName, $data);
    }

    /**
     * Save products price by customer
     *
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveCustomerProductsPrices(array $data)
    {
        $this->connection->insertMultiple($this->getMainTable(), $data);
    }

    /**
     * Save products price by customer
     *
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveCustomersProductPrices(array $data)
    {
        $this->connection->insertMultiple($this->getMainTable(), $data);
    }

    /**
     * @param int $attributeId
     * @param int $productId
     * @param $attributeType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByAttribute(
        $attributeId,
        $productId,
        $attributeType
    ) {
        $select = $this->connection->select()
                                   ->from(['customerprices' => $this->getMainTable()])
                                   ->where('customerprices.attribute_type = ?', $attributeType)
                                   ->where('customerprices.customer_id = ?', $attributeId)
                                   ->where('customerprices.product_id = ?', $productId);
        $data   = $this->connection->fetchRow($select);

        return $data;
    }

    /**
     * @return int|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPriceAttributeId()
    {
        return $this->eavConfig->getAttribute(Product::ENTITY, 'price')->getAttributeId();
    }

    /**
     * @return int|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSpecialPriceAttributeId()
    {
        return $this->eavConfig->getAttribute(Product::ENTITY, 'special_price')->getAttributeId();
    }

    /**
     * Return array customer_id by product_id from table mageworx_customerprices
     *
     * @param int $productId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerIdsByProductId($productId)
    {
        $select = $this->connection->select()
                                   ->from($this->getMainTable(), 'customer_id')
                                   ->where('product_id = ?', $productId);

        $ids = $this->connection->fetchCol($select);

        return array_combine($ids, $ids);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGlobalPricesDataForCustomers()
    {
        $select = $this->connection->select()
                                   ->from($this->getMainTable())
                                   ->where('attribute_type = ?', CustomerPricesModel::TYPE_PRICE_СUSTOMER_GLOBAL)
                                   ->group('customer_id');

        return $this->connection->fetchAll($select);
    }

    /**
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDataByCustomerId($customerId)
    {
        $select = $this->connection->select()
                                   ->from(['customerprices' => $this->getMainTable()])
                                   ->where('customerprices.customer_id = ?', $customerId);

        $select->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.' . $this->getLinkField() . ' = customerprices.product_id',
            [$this->getLinkField()]
        );

        $data = $this->connection->fetchAll($select);

        return $data;
    }

    /**
     * @param int $productId
     * @return null
     */
    public function getTypeId($productId)
    {
        $select = $this->connection->select()->from(
            [$this->getTable('catalog_product_entity')]
        )->where($this->getLinkField(), $productId);

        $data = $this->connection->fetchRow($select);

        if (!empty($data['type_id'])) {
            return $data['type_id'];
        }

        return null;
    }

    /**
     * @param int $entityId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDataByEntityId($entityId)
    {
        $select = $this->connection->select()
                                   ->from(['customerprices' => $this->getMainTable()])
                                   ->where('customerprices.entity_id = ?', $entityId);
        $data   = $this->connection->fetchRow($select);

        return $data;
    }

    /**
     * Delete row in mageworx_catalog_product_index_price by  entityId AND customerId
     *
     * @param int $entityId
     * @param int $customerId
     */
    public function deleteRowInMageworxCatalogProductIndexPrice($entityId, $customerId)
    {
        $this->connection->delete(
            $this->getTable('mageworx_catalog_product_index_price'),
            [
                'entity_id = ?'   => $entityId,
                'customer_id = ?' => $customerId
            ]
        );
    }

    /**
     * Delete row in mageworx_catalog_product_entity_decimal_customer_prices by  entityId AND customerId
     *
     * @param int $entityId
     * @param int $customerId
     */
    public function deleteRowInMageworxCatalogProductEntityDecimalCustomerPrices($entityId, $customerId)
    {
        $priceAttributeId        = $this->getPriceAttributeId();
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();
        $specialPriceAttribute   = [$priceAttributeId, $specialPriceAttributeId];

        $this->connection->delete(
            $this->getTable('mageworx_catalog_product_entity_decimal_customer_prices'),
            [
                $this->getLinkField() . ' = ?' => $entityId,
                'customer_id = ?'              => $customerId,
                'attribute_id IN(?)'           => $specialPriceAttribute
            ]
        );
    }

    /**
     * @param array $ids
     * @param int $customerId
     * @return array
     */
    public function getCalculatedProductsDataByCustomer(array $ids, $customerId)
    {
        $tableName = $this->getTable('mageworx_catalog_product_entity_decimal_customer_prices');
        $select    = $this->connection->select()
                                      ->from($tableName)
                                      ->where('customer_id = ?', $customerId)
                                      ->where($this->getLinkField() . ' IN(?)', $ids);

        return $this->connection->fetchAll($select);
    }

    /**
     * @param int $id
     * @param int $customerId
     * @return array
     */
    public function getCalculatedProductDataByCustomer($id, $customerId)
    {
        $tableName = $this->getTable('mageworx_catalog_product_entity_decimal_customer_prices');
        $select    = $this->connection->select()
                                      ->from($tableName)
                                      ->where('customer_id = ?', $customerId)
                                      ->where($this->getLinkField() . ' = ?', $id);

        return $this->connection->fetchAll($select);
    }

    /**
     * @param array $ids
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCalculatedProductsSpecialPricesByCustomer(array $ids, $customerId)
    {
        $data               = [];
        $tableName          = $this->getTable(
            'mageworx_catalog_product_entity_decimal_customer_prices'
        );
        $linkField          = $this->getLinkField();
        $specialAttributeId = $this->getSpecialPriceAttributeId();

        $select = $this->connection->select()
                                   ->from($tableName)
                                   ->where('customer_id = ?', $customerId)
                                   ->where($linkField . ' IN(?)', $ids)
                                   ->where('attribute_id = ?', $specialAttributeId);

        foreach ($this->connection->fetchAll($select) as $item) {
            $data[$item[$linkField]]['value'] = $item['value'];
        }

        return $data;
    }

    /**
     * @param int $id
     * @param int $customerId
     * @return array
     */
    public function getRulesProductDataByCustomer($id, $customerId)
    {
        $tableName = $this->getTable('mageworx_catalogrule_product_price');
        $select    = $this->connection->select()
                                      ->from($tableName)
                                      ->where('customer_id = ?', $customerId)
                                      ->where('product_id = ?', $id);

        return $this->connection->fetchAll($select);
    }

    /**
     * @param array $ids
     * @param $customerId
     * @return array
     */
    public function getRulesProductsDataByCustomer($ids, $customerId)
    {
        $tableName = $this->getTable('mageworx_catalogrule_product_price');
        $select    = $this->connection->select()
                                      ->from($tableName)
                                      ->where('customer_id = ?', $customerId)
                                      ->where('product_id IN(?)', $ids);

        return $this->connection->fetchAll($select);
    }


    /**
     * @param int $productId
     * @return bool
     */
    public function hasSpecialAttributeByProductId($productId)
    {
        $tableName               = $this->getTable('catalog_product_entity_decimal');
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();

        $select = $this->connection->select()
                                   ->from($tableName)
                                   ->where($this->getLinkField() . ' = ?', $productId)
                                   ->where('attribute_id = ?', $specialPriceAttributeId);

        return !empty($this->connection->fetchRow($select));
    }

    /**
     * @param array $productIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIdsWithSpecialAttribute($productIds)
    {
        $data                    = [];
        $tableName               = $this->getTable('catalog_product_entity_decimal');
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();
        $linkField               = $this->getLinkField();

        $select = $this->connection->select()
                                   ->from($tableName, $linkField)
                                   ->where($linkField . ' IN(?)', $productIds)
                                   ->where('attribute_id = ?', $specialPriceAttributeId);


        foreach ($this->connection->fetchAll($select) as $item) {
            $data[$item[$linkField]] = $item[$linkField];
        }

        return $data;
    }

    /**
     * @param int $productId
     */
    public function addRowWithSpecialAttribute($productId)
    {
        $tableName               = $this->getTable('catalog_product_entity_decimal');
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();

        $data = [
            'value_id'            => '',
            'attribute_id'        => $specialPriceAttributeId,
            'store_id'            => $this->getStoreIdProductPrice($productId),
            $this->getLinkField() => $productId,
            'value'               => null
        ];

        $this->connection->insert($tableName, $data);
    }

    /**
     * @param array $productIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addRowsWithSpecialAttribute($productIds)
    {
        $data                    = [];
        $tableName               = $this->getTable('catalog_product_entity_decimal');
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();

        foreach ($productIds as $productId) {
            $data[] = [
                'value_id'            => '',
                'attribute_id'        => $specialPriceAttributeId,
                'store_id'            => 0,
                $this->getLinkField() => $productId,
                'value'               => null
            ];
        }

        $this->connection->insertMultiple($tableName, $data);
    }

    /**
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasAssignCustomer($customerId)
    {
        $select = $this->connection->select()
                                   ->from(['customerprices' => $this->getMainTable()])
                                   ->where('customerprices.customer_id = ?', $customerId);
        $data   = $this->connection->fetchRow($select);

        return (bool)$data;
    }

    /**
     * @param $productId
     * @return string
     */
    protected function getStoreIdProductPrice($productId)
    {
        $tableName        = $this->getTable('catalog_product_entity_decimal');
        $priceAttributeId = $this->getPriceAttributeId();

        $select = $this->connection->select()
                                   ->from($tableName)
                                   ->where($this->getLinkField() . ' = ?', $productId)
                                   ->where('attribute_id = ?', $priceAttributeId);

        $data = $this->connection->fetchRow($select);

        return !empty($data) ? $data['store_id'] : '0';
    }

    /**
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getLinkProductIdsToTypeId($productIds)
    {
        $linkField = $this->getLinkField();
        $select    = $this->connection->select()
                                      ->from([$this->getTable('catalog_product_entity')], [$linkField, 'type_id'])
                                      ->where($linkField . ' IN(?)', $productIds);

        return $this->connection->fetchPairs($select);
    }

    /**
     * @param CustomerPricesCollection $collection
     * @return CustomerPricesCollection
     */
    public function joinEmailCustomer($collection)
    {
        /* @var CustomerPricesCollection $collection */
        $collection->getSelect()->joinLeft(
            ['email' => $this->getTable('customer_entity')],
            'email.entity_id = main_table.customer_id',
            ['email']
        );

        return $collection;
    }

    /**
     * @param CustomerPricesCollection $collection
     * @return CustomerPricesCollection
     * @throws \Exception
     */
    public function joinSkuProduct($collection)
    {
        /* @var CustomerPricesCollection $collection */
        $collection->getSelect()->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.' . $this->getLinkField() . ' = main_table.product_id',
            ['sku']
        );

        return $collection;
    }

    /**
     * @param CustomerPricesCollection $collection
     * @return CustomerPricesCollection
     * @throws \Exception
     */
    public function joinProductEntity($collection)
    {
        /* @var CustomerPricesCollection $collection */
        $collection->getSelect()->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.' . $this->getLinkField() . ' = main_table.product_id',
            ['entity_id']
        );

        return $collection;
    }

    /**
     * @param array $customerIds
     * @param int $customerPriceType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFullCustomersPricesData($customerIds = [], $customerPriceType = 1)
    {
        $select = $this->connection->select()
                                   ->from(['customerprices' => $this->getMainTable()])
                                   ->where('customerprices.attribute_type = ?', $customerPriceType);

        if (!empty($customerIds)) {
            $select->where('customerprices.customer_id IN(?)', $customerIds);
        }

        $select->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.entity_id = customerprices.product_id',
            ['sku']
        );

        $select->joinLeft(
            ['email' => $this->getTable('customer_entity')],
            'email.entity_id = customerprices.customer_id',
            ['email']
        );

        $data = $this->connection->fetchAll($select);

        return $data;
    }

    /**
     * Get product ids assign on customer
     *
     * @param int $customerId
     * @param string $column
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIdsByCustomerId($customerId, $column)
    {
        $productIds = [];
        $select     = $this->connection->select()
                                       ->from(['customerprices' => $this->getMainTable()], ['product_id'])
                                       ->where(
                                           'customerprices.attribute_type = ?',
                                           CustomerPricesModel::TYPE_PRICE_CUSTOMER
                                       )
                                       ->where('customerprices.customer_id = ?', $customerId)
                                       ->group('product_id');

        $select->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.' . $this->getLinkField() . ' = customerprices.product_id',
            ['entity_id']
        );

        $selectData = $this->connection->fetchAll($select);
        $column     = $column == 'entity_id' ? 'entity_id' : 'product_id';

        foreach ($selectData as $data) {
            $productIds[$data[$column]] = $data[$column];
        }

        return $productIds;
    }

    /**
     * return array {product_id}-{price}-{special_price}
     *
     * @param int $customerId
     * @param int $customerPriceType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerPricesDataFromMainTable($customerId, $customerPriceType = 1)
    {
        $data   = [];
        $select = $this->connection->select()
                                   ->from($this->getMainTable(), ['product_id', 'price', 'special_price'])
                                   ->where('customer_id = ?', $customerId)
                                   ->where('attribute_type = ?', $customerPriceType);

        foreach ($this->connection->fetchAll($select) as $item) {
            $productId                         = $item['product_id'];
            $data[$productId]['product_id']    = $productId;
            $data[$productId]['price']         = $item['price'];
            $data[$productId]['special_price'] = $item['special_price'];
        }

        return $data;
    }

    /**
     * get array with id:{price:-10%,special_price:-20%}
     *
     * @param int $customerId
     * @param int $customerPriceType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsPricesByCustomerId($customerId, $customerPriceType)
    {
        $productData = [];
        $select      = $this->connection->select()
                                        ->from(
                                            ['customerprices' => $this->getMainTable()],
                                            ['price', 'special_price']
                                        )
                                        ->where('customerprices.customer_id = ?', $customerId)
                                        ->where('customerprices.attribute_type = ?', $customerPriceType)
                                        ->group('product_id');

        $select->joinLeft(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.' . $this->getLinkField() . ' = customerprices.product_id',
            ['entity_id']
        );

        $customerData = $this->connection->fetchAll($select);

        if (!is_array($customerData)) {
            return [];
        }

        foreach ($customerData as $data) {
            $productData[$data['entity_id']] = array(
                "price"         => $data['price'],
                "special_price" => $data['special_price']
            );
        }

        return $productData;
    }

    /**
     * @param int $productId
     * @param string $linkField
     * @return int
     */
    public function getLinkFieldId($productId, $linkField)
    {
        $tableName = $this->getTable('catalog_product_entity');
        $select    = $this->connection->select()
                                      ->from($tableName, $linkField)
                                      ->where('entity_id = ?', $productId);

        return $this->connection->fetchOne($select);
    }

    /**
     * @param array $productIds
     * @param string $linkField
     * @return array
     */
    public function getLinkFieldIds($productIds, $linkField)
    {
        $select = $this->connection->select()
                                   ->from($this->getTable('catalog_product_entity'), $linkField)
                                   ->where('entity_id IN(?)', $productIds);

        $ids = $this->connection->fetchCol($select);

        return array_combine($ids, $ids);
    }

    /**
     * Retrieve the array. Format LinkField => EntityId:
     * 'row_id' => 'entity_id' for EE,
     * 'entity_id' => 'entity_id' for CE
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductIdsPairs($productIds, $linkField)
    {
        $select = $this->connection->select()
                                   ->from(
                                       $this->getTable('catalog_product_entity'),
                                       [
                                           'id' => $linkField,
                                           'entity_id'
                                       ]
                                   )
                                   ->where('entity_id IN(?)', $productIds);

        $data = $this->connection->fetchPairs($select);

        return $data;
    }

    /**
     * @param int $productId
     * @return string
     */
    public function getEntityId($productId)
    {
        $select = $this->connection->select()
                                   ->from($this->getTable('catalog_product_entity'), 'entity_id')
                                   ->where($this->getLinkField() . ' = ?', $productId);

        return $this->connection->fetchOne($select);
    }

    /**
     * @param array $referenceIds
     * @return array
     * @throws \Exception
     */
    public function getEntityIds($referenceIds)
    {
        $select = $this->connection->select()
                                   ->from(
                                       $this->getTable('catalog_product_entity'),
                                       ['entity_id']
                                   )
                                   ->where($this->getLinkField() . ' IN(?)', $referenceIds);

        $ids = $this->connection->fetchCol($select);

        return array_combine($ids, $ids);
    }

    /**
     *
     * @param array $types
     * @return array
     * @throws \Exception
     */
    public function getProductIdsWithProductTypes($types)
    {
        $ids       = [];
        $linkField = $this->getLinkField();
        $select    = $this->connection->select()
                                      ->from(
                                          $this->getTable('catalog_product_entity'),
                                          [$linkField, 'type_id']
                                      )
                                      ->where('type_id' . ' IN(?)', $types);

        foreach ($this->connection->fetchAll($select) as $item) {
            $productId                     = $item[$linkField];
            $ids[$productId]['product_id'] = $productId;
            $ids[$productId]['type_id']    = $item['type_id'];
        }

        return $ids;
    }

    /**
     *
     * @param array $types
     * @return array
     * @throws \Exception
     */
    public function getAllProductIdsByAllowedProductTypes($types)
    {
        $linkField = $this->getLinkField();
        $select    = $this->connection->select()
                                      ->from(
                                          $this->getTable('catalog_product_entity'),
                                          [$linkField]
                                      )
                                      ->where('type_id' . ' IN(?)', $types);

        $ids = $this->connection->fetchCol($select);

        return array_combine($ids, $ids);
    }

    /**
     * @param array $entityIds
     * @param int $customerId
     * @throws \Exception
     */
    public function deleteRowsFromTableMageworxCatalogProductIndexPrice($entityIds, $customerId)
    {
        $this->connection->delete(
            $this->getTable('mageworx_catalog_product_index_price'),
            [
                'customer_id = ?'  => $customerId,
                'entity_id  IN(?)' => $entityIds
            ]
        );
    }

    /**
     * @param array $productsIds
     * @param int $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteRowsFromTableMageworxCatalogProductEntityDecimalCustomerPrices($productsIds, $customerId)
    {
        $priceAttributeId        = $this->getPriceAttributeId();
        $specialPriceAttributeId = $this->getSpecialPriceAttributeId();
        $specialPriceAttribute   = [$priceAttributeId, $specialPriceAttributeId];

        $this->connection->delete(
            $this->getTable('mageworx_catalog_product_entity_decimal_customer_prices'),
            [
                'customer_id = ?'                => $customerId,
                'attribute_id IN(?)'             => $specialPriceAttribute,
                $this->getLinkField() . ' IN(?)' => $productsIds
            ]
        );
    }

    /**
     * @param int $customerId
     * @param int $typePrice
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIdsByCustomerIdAndPriceType($customerId, $typePrice)
    {
        $ids    = [];
        $select = $this->connection->select()
                                   ->from($this->getMainTable(), 'product_id')
                                   ->where('customer_id = ?', $customerId)
                                   ->where('attribute_type = ?', $typePrice);

        foreach ($this->connection->fetchAll($select) as $item) {
            $ids[$item['product_id']] = $item['product_id'];
        }

        return $ids;
    }

    /**
     * Return array in format {product_id}-{price}-{special_price}
     *
     * @param int $customerId
     * @param int $typePrice
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsDataByCustomerIdAndPriceType($customerId, $typePrice)
    {
        $data   = [];
        $select = $this->connection->select()
                                   ->from($this->getMainTable(), ['product_id', 'price', 'special_price'])
                                   ->where('customer_id = ?', $customerId)
                                   ->where('attribute_type = ?', $typePrice);

        foreach ($this->connection->fetchAll($select) as $item) {
            $productId                         = $item['product_id'];
            $data[$productId]['price']         = $item['price'];
            $data[$productId]['special_price'] = $item['special_price'];
        }

        return $data;
    }

    /**
     * @param int $customerId
     * @param int $typePrice
     * @param string $price
     * @param string $specialPrice
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsIdsWithCorrectGlobalPrice($customerId, $typePrice, $price, $specialPrice)
    {
        $select = $this->connection->select()
                                   ->from($this->getMainTable(), 'product_id')
                                   ->where('customer_id = ?', $customerId)
                                   ->where('attribute_type = ?', $typePrice)
                                   ->where('price = ?', $price)
                                   ->where('special_price = ?', $specialPrice);

        $ids = $this->connection->fetchCol($select);

        return array_combine($ids, $ids);
    }

    /**
     * @param int $customerId
     * @param int $typePrice
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductDataByCustomerIdAndTypePrice($customerId, $typePrice)
    {
        $select = $this->connection->select()
                                   ->from($this->getMainTable())
                                   ->where('customer_id = ?', $customerId)
                                   ->where('attribute_type = ?', $typePrice);

        return $this->connection->fetchRow($select);
    }

    /**
     * @param array $ids
     * @param int $customerId
     * @return array
     * @throws \Exception
     */
    public function getPairsProductIdPriceValueWithMinValue(array $ids, $customerId)
    {
        $minPairs  = [];
        $tableName = $this->getTable(
            'mageworx_catalog_product_entity_decimal_customer_prices'
        );

        $select = $this->connection->select()
                                   ->from($tableName)
                                   ->where('customer_id = ?', $customerId)
                                   ->where($this->getLinkField() . ' IN(?)', $ids)
                                   ->where('value is not NULL');

        $select->reset(\Zend_Db_Select::COLUMNS)
               ->columns(
                   array(
                       'product_id' => $tableName . '.' . $this->getLinkField(),
                       'min_price'  => $tableName . '.value',
                   )
               );

        foreach ($this->connection->fetchPairs($select) as $key => $value) {
            if (!array_key_exists($key, $minPairs)) {
                $minPairs[$key] = $value;
            }

            if (array_key_exists($key, $minPairs) && $minPairs[$key] < $value) {
                $minPairs[$key] = $value;
            }
        }

        return $minPairs;
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getProductIdBySku($sku)
    {
        $tableName = $this->getTable('catalog_product_entity');
        $select    = $this->connection->select()
                                      ->from($tableName, $this->getLinkField())
                                      ->where('sku = ?', $sku);

        return $this->connection->fetchOne($select);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getLinkField()
    {
        return $this->helperCalculate->getLinkField();
    }
}