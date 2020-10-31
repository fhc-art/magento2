<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use MageWorx\CustomerPrices\Helper\Data as HelperData;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices as ResourceCustomerPrices;
use MageWorx\CustomerPrices\Model\ResourceModel\Product\Indexer\CustomerPrice as IndexCustomPrice;
use Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;
use Magento\Framework\Indexer\IndexerRegistry;
use MageWorx\CustomerPrices\Model\CustomerPricesRepository;

class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var HelperCustomer
     */
    private $helperCustomer;

    /**
     * @var ResourceCustomerPrices
     */
    private $customerPricesResourceModel;

    /**
     * @var IndexCustomPrice
     */
    private $indexer;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var HelperProduct
     */
    protected $helperProduct;

    /**
     * @var CustomerPricesRepository
     */
    protected $customerPricesRepository;

    /**
     * CatalogProductSaveAfterObserver constructor.
     *
     * @param HelperData $helperData
     * @param HelperCustomer $helperCustomer
     * @param HelperProduct $helperProduct
     * @param ResourceCustomerPrices $customerPricesResourceModel
     * @param IndexCustomPrice $indexer
     * @param IndexerRegistry $indexerRegistry
     * @param CustomerPricesRepository $customerPricesRepository
     */
    public function __construct(
        HelperData $helperData,
        HelperCustomer $helperCustomer,
        HelperProduct $helperProduct,
        ResourceCustomerPrices $customerPricesResourceModel,
        IndexCustomPrice $indexer,
        IndexerRegistry $indexerRegistry,
        CustomerPricesRepository $customerPricesRepository
    ) {
        $this->helperData                  = $helperData;
        $this->helperCustomer              = $helperCustomer;
        $this->helperProduct               = $helperProduct;
        $this->customerPricesResourceModel = $customerPricesResourceModel;
        $this->indexer                     = $indexer;
        $this->indexerRegistry             = $indexerRegistry;
        $this->customerPricesRepository    = $customerPricesRepository;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        if (!$product->getId($product)) {
            return $this;
        }

        if ($product->isObjectNew()) {
            $this->addGlobalPrices($product);
        }

        $productId     = $this->helperProduct->getProductId($product);
        $customerIds   = $this->customerPricesResourceModel->getCustomerIdsByProductId($productId);
        $productTypeId = $product->getTypeId();
        if (!empty($productTypeId) && !empty($customerIds)) {

            /* set data in catalog_product_entity_decimal */
            if (!$this->customerPricesResourceModel->hasSpecialAttributeByProductId($productId)) {
                $this->customerPricesResourceModel->addRowWithSpecialAttribute($productId);
            }

            /* reindex data */
            $this->indexer->setTypeId($productTypeId);
            $this->indexer->reindexEntityCustomer([$productId], $customerIds);

            /* add notification need reindex catalogrule_rule */
            if ($this->helperData->isEnabledCustomerPriceInCatalogPriceRule()) {
                $this->indexerRegistry->get(RuleProductProcessor::INDEXER_ID)->invalidate();
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addGlobalPrices($product)
    {
        $productId = $this->helperProduct->getProductId($product);

        $data = $this->customerPricesResourceModel->getGlobalPricesDataForCustomers();

        $dataToSave = [];

        foreach ($data as $datum) {

            if ($datum['price'] || $datum['special_price']) {
                $datum['entity_id']  = null;
                $datum['product_id'] = $productId;
                $dataToSave[]        = $datum;
            }
        }

        if ($dataToSave) {
            $this->customerPricesResourceModel->saveCustomersProductPrices($dataToSave);
        }
    }
}