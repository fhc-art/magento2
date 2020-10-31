<?php
/**
 * This file is part of the frameless project.
 *
 * Copyright (c) atmosol
 *
 * All rights reserved.
 */

namespace Atmosol\RocketIntegration\Cron;

use Atmosol\RocketIntegration\Logger\Logger;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item;
use Magento\Store\Model\ScopeInterface;

/**
 * ProductInventoryBatchUpdate class
 */
class ProductInventoryBatchUpdate
{
    const CONFIG_PATH_ROCKET_URI = 'atmosol/rocketintegration/rocket_url';

    /** @var CollectionFactory */
    protected $collectionFactory;
    /** @var StockItemRepositoryInterface */
    protected $stockItemRepository;
    /** @var Logger */
    protected $logger;
    /** @var Item */
    protected $itemResource;
    /** @var ZendClientFactory */
    protected $httpClientFactory;
    /** @var ScopeConfigInterface */
    protected $config;

    /**
     * @param CollectionFactory            $collectionFactory
     * @param StockItemRepositoryInterface $stockItemRepository
     * @param Logger                       $logger
     * @param Item                         $itemResource
     * @param ZendClientFactory            $httpClientFactory
     * @param ScopeConfigInterface         $config
     */
    public function __construct(CollectionFactory $collectionFactory, StockItemRepositoryInterface $stockItemRepository, Logger $logger, Item $itemResource, ZendClientFactory $httpClientFactory, ScopeConfigInterface $config)
    {
        $this->collectionFactory = $collectionFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->logger = $logger;
        $this->itemResource = $itemResource;
        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
    }

    /**
     *
     */
    public function execute()
    {
        // Get Rocket URI from config. Skip cron if not set
        $rocketUri = 'http://207.106.217.102:8181'; // TODO: remove and replace with following line
        //$rocketUri = $this->config->getValue(self::CONFIG_PATH_ROCKET_URI, ScopeInterface::SCOPE_STORE);
        if (!$rocketUri) {
            return;
        }
        $rocketUri .= '/restLookup/syncqoh';

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(['sku']);
        $collection->setPageSize(500);
        $collection->addAttributeToFilter(Product::STATUS, ['eq' => Product\Attribute\Source\Status::STATUS_ENABLED]);
        $lastPage = $collection->getLastPageNumber();

        for ($pageNum = 1; $pageNum <= $lastPage; $pageNum++) {
            $collection->setCurPage($pageNum);

            // Grab all SKUs from the current page
            $pageSkus = [];

            /** @var Product $product */
            foreach ($collection as $product) {
                $pageSkus[] = ['sku' => $product->getSku()];
            }

            // Build POST body and HTTP client
            $postBody = [
                'mode'   => 'L',
                'sku_in' => $pageSkus,
            ];

            /** @var ZendClient $client */
            $client = $this->httpClientFactory->create();
            $client->setConfig([
                'maxredirects' => 0,
                'timeout' => 30,
            ]);

            // Send request
            $client->setUri($rocketUri);
            $client->setParameterPost($postBody);
            $response = $client->request(\Zend\Http\Request::METHOD_POST)->getBody();

            $skuQtys = [];

            foreach ($response['skuqty_out'] as $sqPair) {
                $skuQtys[$sqPair['prod_sku']] = $sqPair['prod_qty'];
            }

            /** @var Product $product */
            foreach ($collection as $product) {
                $sku = $product->getSku();
                $quantity = $skuQtys[$sku];

                if (@array_key_exists($sku, $skuQtys)) {
                    $this->logger->info('Batch Inventory Update: No quantity returned for product: "' . $product->getId() . '" (sku "' . $product->getSku() . '")');
                } else {

                    try {
                        $stockItem = $this->stockItemRepository->get($product->getId());
                    } catch (NoSuchEntityException $e) {
                        $this->logger->info('Batch Inventory Update: Stock item not found for product "' . $product->getId() . '" (sku "' . $product->getSku() . '")');
                        break;
                    }

                    if ($stockItem->getQty() !== $quantity) {
                        $stockItem->setQty($quantity);
                        try {
                            $this->itemResource->save($stockItem);
                        } catch (AlreadyExistsException $e) {
                            $this->logger->info('Batch Inventory Update: Stock item already exists, error on saving for product "' . $product->getId() . '" (sku "' . $product->getSku() . '")');
                        } catch (Exception $e) {
                            $this->logger->info('Batch Inventory Update: Error on saving for product "' . $product->getId() . '" (sku "' . $product->getSku() . '")', ['exceptionMessage' => $e->getMessage()]);
                        }
                    }
                }
            }
        }
    }
}
