<?php

/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Select;
use Magento\Framework\Indexer\DimensionFactory;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use Magento\Catalog\Model\ResourceModel\Product\Indexer\LinkedProductSelectBuilderByIndexPrice;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Model\Session;
use Magento\Framework\EntityManager\MetadataPool;

class GetEntityIdChildOfConfigurableProductWithMinPrice
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var BaseSelectProcessorInterface
     */
    private $baseSelectProcessor;

    /**
     * @var IndexScopeResolverInterface|null
     */
    private $priceTableResolver;

    /**
     * @var DimensionFactory|null
     */
    private $dimensionFactory;

    /**
     * @var HelperCustomer
     */
    protected $helperCustomer;

    /**
     * GetEntityIdChildOfConfigurableProductWithMinPrice constructor.
     *
     * @param HelperCustomer $helperCustomer
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     * @param Session $customerSession
     * @param MetadataPool $metadataPool
     * @param BaseSelectProcessorInterface|null $baseSelectProcessor
     * @param IndexScopeResolverInterface|null $priceTableResolver
     * @param DimensionFactory|null $dimensionFactory
     */
    public function __construct(
        HelperCustomer $helperCustomer,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        Session $customerSession,
        MetadataPool $metadataPool,
        BaseSelectProcessorInterface $baseSelectProcessor = null,
        IndexScopeResolverInterface $priceTableResolver = null,
        DimensionFactory $dimensionFactory = null
    ) {
        $this->helperCustomer      = $helperCustomer;
        $this->storeManager        = $storeManager;
        $this->resource            = $resourceConnection;
        $this->customerSession     = $customerSession;
        $this->metadataPool        = $metadataPool;
        $this->baseSelectProcessor = (null !== $baseSelectProcessor)
            ? $baseSelectProcessor : ObjectManager::getInstance()->get(BaseSelectProcessorInterface::class);
        $this->priceTableResolver  = $priceTableResolver
            ?? ObjectManager::getInstance()->get(IndexScopeResolverInterface::class);
        $this->dimensionFactory    = $dimensionFactory ?? ObjectManager::getInstance()->get(DimensionFactory::class);
    }

    /**
     * Get entity_id child of configurable product with minimal price
     *
     * @param LinkedProductSelectBuilderByIndexPrice $subject
     * @param callable $proceed
     * @param int $productId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundBuild(LinkedProductSelectBuilderByIndexPrice $subject, callable $proceed, $productId)
    {
        $customerId = $this->helperCustomer->getCurrentCustomerId();
        if (!$customerId) {
            return $proceed($productId);
        }

        $productTable    = $this->resource->getTableName('catalog_product_entity');
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $websiteId       = $this->storeManager->getStore()->getWebsiteId();
        $linkField       = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();

        $priceSelect = $this->resource->getConnection()->select()
                                      ->from(['parent' => $productTable], '')
                                      ->joinInner(
                                          ['link' => $this->resource->getTableName('catalog_product_relation')],
                                          "link.parent_id = parent.$linkField",
                                          []
                                      )->joinInner(
                [BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS => $productTable],
                sprintf('%s.entity_id = link.child_id', BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS),
                ['IFNULL(`tm`.`entity_id`,`child`.`entity_id`)']
            )->joinInner(
                [
                    't' => $this->priceTableResolver->resolve('catalog_product_index_price', [])
                ],
                sprintf('t.entity_id = %s.entity_id', BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS),
                []
            )->joinLeft(
                [
                    'tm' => $this->priceTableResolver->resolve('mageworx_catalog_product_index_price', [])
                ],
                sprintf(
                    'tm.entity_id = %s.entity_id and tm.customer_id = %s',
                    BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS,
                    $customerId
                ),
                []
            )->where('parent.entity_id = ?', $productId)
                                      ->where('t.website_id = ?', $websiteId)
                                      ->where('t.customer_group_id = ?', $customerGroupId)
                                      ->order('LEAST(IFNULL(tm.min_price, t.min_price),t.min_price) ' . Select::SQL_ASC)
                                      ->order('t.min_price ' . Select::SQL_ASC)
                                      ->order(
                                          BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS . '.' . $linkField . ' ' . Select::SQL_ASC
                                      )
                                      ->limit(1);
        $priceSelect = $this->baseSelectProcessor->process($priceSelect);

        return [$priceSelect];
    }
}