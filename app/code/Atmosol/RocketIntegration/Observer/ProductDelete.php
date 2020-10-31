<?php
/**
 * This file is part of the frameless project.
 *
 * Copyright (c) atmosol
 *
 * All rights reserved.
 */

namespace Atmosol\RocketIntegration\Observer;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Atmosol\RocketIntegration\Logger\Logger;
use Magento\Store\Model\ScopeInterface;

/**
 * ProductDelete class
 */
class ProductDelete implements ObserverInterface
{
    const CONFIG_PATH_ROCKET_URI = 'atmosol/rocketintegration/rocket_url';

    /** @var ZendClientFactory */
    protected $httpClientFactory;
    /** @var ScopeConfigInterface */
    protected $config;
    /** @var Logger */
    protected $logger;

    /**
     * @param ZendClientFactory    $httpClientFactory
     * @param ScopeConfigInterface $config
     * @param Logger               $logger
     */
    public function __construct(ZendClientFactory $httpClientFactory, ScopeConfigInterface $config, Logger $logger) {
        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        // Get Rocket URI from config. Skip observer if not set
        $rocketUri = 'http://207.106.217.102:8181'; // TODO: remove and replace with following line
        //$rocketUri = $this->config->getValue(self::CONFIG_PATH_ROCKET_URI, ScopeInterface::SCOPE_STORE);
        if (!$rocketUri) {
            return;
        }
        $rocketUri .= '/restMaintain/product.maint';

        $product = $observer->getData('product');
        $productArray = $product->toFlatArray();

        $postBody = [
            'mode'                   => 'D',
            'sku'                    => $product->getSku(),
        ];

        /** @var ZendClient $client */
        $client = $this->httpClientFactory->create();
        $client->setConfig([
            'maxredirects' => 0,
            'timeout' => 30,
        ]);

        $client->setUri($rocketUri);
        $client->setRawData(json_encode(["REC" => $postBody]));

        try {
            $response = $client->request(\Zend\Http\Request::METHOD_POST);
            $this->logger->info(json_encode($response->getBody(), JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
