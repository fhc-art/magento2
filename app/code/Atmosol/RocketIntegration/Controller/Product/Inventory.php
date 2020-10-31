<?php
/**
 * This file is part of the frameless project.
 *
 * Copyright (c) atmosol
 *
 * All rights reserved.
 */

namespace Atmosol\RocketIntegration\Controller\Product;

use Atmosol\RocketIntegration\Logger\Logger;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Store\Model\ScopeInterface;

/**
 * Inventory class
 */
class Inventory extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    const CONFIG_PATH_ROCKET_URI = 'rocketintegration/general/rocket_url';

    /** @var ZendClientFactory */
    protected $httpClientFactory;
    /** @var ScopeConfigInterface */
    protected $config;
    /** @var ProductRepositoryInterface */
    protected $productRepository;
    /** @var StockItemRepository */
    protected $stockItemRepository;
    /** @var Item */
    protected $itemResource;
    /** @var Logger */
    protected $logger;
    /** @var JsonFactory */
    protected $resultJsonFactory;

    /**
     * @param Context                    $context
     * @param ZendClientFactory          $httpClientFactory
     * @param ScopeConfigInterface       $config
     * @param ProductRepositoryInterface $productRepository
     * @param StockItemRepository        $stockItemRepository
     * @param Item                       $itemResource
     * @param Logger                     $logger
     * @param JsonFactory                $resultJsonFactory
     */
    public function __construct(Context $context, ZendClientFactory $httpClientFactory, ScopeConfigInterface $config, ProductRepositoryInterface $productRepository, StockItemRepository $stockItemRepository, Item $itemResource, Logger $logger, JsonFactory $resultJsonFactory)
    {
        parent::__construct($context);

        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->stockItemRepository = $stockItemRepository;
        $this->itemResource = $itemResource;
        $this->logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $body = $this->getRequest()->getContent();
        $bodyData = json_decode($body, true);
        $sku = isset($bodyData['sku']) ? $bodyData['sku'] : '';

        $result = false;

        try {
            if (empty($sku)) {
                throw new NoSuchEntityException();
            }
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            $this->logger->info('Inventory Check: Product not found for sku "' . $sku . '"');
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($result);
            return $resultJson;
        }


        try {
            //$stockItem = $this->stockItemRepository->get($product->getId());
            $stockItem = $product->getExtensionAttributes()->getStockItem();
            if (null === $stockItem) {
                throw new NoSuchEntityException();
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->info('Inventory Check: Stock item not found for product "' . $product->getId() . '" (sku "' . $product->getSku() . '")');
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($result);
            return $resultJson;
        }

        $quantity = $stockItem->getQty();

        // Get Rocket URI from config. Skip execution if not set
        $rocketUri = 'http://207.106.217.102:8181'; // TODO: remove and replace with following line
        $responseData = null;
        //$rocketUri = $this->config->getValue(self::CONFIG_PATH_ROCKET_URI, ScopeInterface::SCOPE_STORE);
        if (!$rocketUri) {
            $this->logger->info('Inventory Check: No rocket url set in configuration.');
        } else {
            $rocketUri .= '/restLookup/getqoh';

            /** @var ZendClient $client */
            $client = $this->httpClientFactory->create();
            $client->setConfig([
                'maxredirects' => 0,
                'timeout' => 10,
            ]);

            $client->setUri($rocketUri);
            $client->setParameterGet(['sku' => $sku]);

            try {
                $response = $client->request(ZendClient::GET)->getBody();
                $responseData = json_decode($response, true);
            } catch (Exception $e) {
                $this->logger->info('Inventory Check: Error when contacting Rocket. Error message: '.$e->getMessage());
            }
        }

        if (null !== $responseData && isset($responseData['getqoh']) && isset($responseData['getqoh']['QTY']) && !empty($responseData['getqoh']['QTY'])) {
            $quantity = intval($responseData['getqoh']['QTY']);
        } else {
            $this->logger->info('Inventory Check: Rocket data not found for "' . $product->getId() . '" (sku "' . $product->getSku() . '")');
        }

        $stockMessage = $quantity <= $stockItem->getMinQty()
            ? 'Out of Stock'
            : 'In Stock';

        $result = ['quantity' => $quantity, 'stock_message' => $stockMessage, 'response' => $responseData];

        if ($quantity <= $stockItem->getMinQty()) {
            $result['magento_in_stock'] = false;
        } else {
            $result['magento_in_stock'] = true;
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($result);

        if ($stockItem->getQty() !== $quantity) {
            $stockItem->setQty($quantity);
            try {
                $this->itemResource->save($stockItem);
            } catch (AlreadyExistsException $e) {
                $this->logger->info('Inventory Check: Stock item already exists, error on saving for product "' . $product->getId() . '" (sku "' . $product->getSku() . '")');
            } catch (Exception $e) {
                $this->logger->info('Inventory Check: Error on saving for product "' . $product->getId() . '" (sku "' . $product->getSku() . '")', ['exceptionMessage' => $e->getMessage()]);
            }
        }

        return $resultJson;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
