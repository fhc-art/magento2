<?php
/**
 * This file is part of the frameless project.
 *
 * Copyright (c) atmosol
 *
 * All rights reserved.
 */

namespace Atmosol\RocketIntegration\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductTierPriceInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\ZendClientFactory;
use Atmosol\RocketIntegration\Logger\Logger;
use Magento\Framework\Phrase;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;

/**
 * ProductSave class
 */
class ProductSave implements ObserverInterface
{
    const CONFIG_PATH_ROCKET_URI = 'atmosol/rocketintegration/rocket_url';

    /** @var ZendClientFactory */
    protected $httpClientFactory;
    /** @var ScopeConfigInterface */
    protected $config;
    /** @var Logger */
    protected $logger;
    /** @var ProductTaxClassSource */
    protected $productTaxClassSource;
    /** @var StockItemRepositoryInterface */
    protected $stockItemRepository;
    /** @var GroupRepositoryInterface */
    protected $customerGroupRepository;

    /**
     * @param ZendClientFactory    $httpClientFactory
     * @param ScopeConfigInterface $config
     * @param Logger               $logger
     */
    public function __construct(ZendClientFactory $httpClientFactory, ScopeConfigInterface $config, Logger $logger, ProductTaxClassSource $productTaxClassSource, StockItemRepositoryInterface $stockItemRepository, GroupRepositoryInterface $customerGroupRepository) {
        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
        $this->logger = $logger;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->stockItemRepository = $stockItemRepository;
        $this->customerGroupRepository = $customerGroupRepository;
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

        /** @var Product $product */
        $product = $observer->getData('product');

        // Note about this stockItem - if any of the advanced inventory values changes, this won't be caught until the next time this is run. The stock data saves after the product does.
        $stockItem = $product->getExtensionAttributes()->getStockItem();

        $weight = $product->getWeight() !== null ? $product->getWeight() : '';
        $cost = $product->getCost() !== null ? $product->getCost() : '';
        $taxSelection = $this->productTaxClassSource->getOptionText($product->getTaxClassId());
        if (is_string($taxSelection)) {
            if ('None' === $taxSelection) {
                $taxable = false;
            } else {
                $taxable = true;
            }
        } else {
            /** @var Phrase $taxSelection */
            if ($taxSelection->getText() === 'None') {
                $taxable = false;
            } else {
                $taxable = true;
            }
        }
        $backorderable = $stockItem && ($stockItem->getBackorders() > 0);
        $countryOfManufacture = $product->getCountryOfManufacture() !== null ? $product->getCountryOfManufacture() : '';

        $productTaglineAttribute = $product->getCustomAttribute('product_tagline');
        $productTagline = $productTaglineAttribute ? $productTaglineAttribute->getValue() : '';

        $crlPartAttribute = $product->getCustomAttribute('crl_part');
        $crlPart = $crlPartAttribute ? $crlPartAttribute->getValue() : '';

        $productGroupAttribute = $product->getCustomAttribute('product_group');
        $productGroup = $productGroupAttribute ? $productGroupAttribute->getValue() : '';

        $productClassAttribute = $product->getCustomAttribute('product_class');
        $productClass = $productClassAttribute ? $productClassAttribute->getValue() : '';

        $leadTimeAttribute = $product->getCustomAttribute('lead_time');
        $leadTime = $leadTimeAttribute ? $leadTimeAttribute->getValue() : '';

        $primaryVendorAttribute = $product->getCustomAttribute('primary_vendor');
        $primaryVendor = $primaryVendorAttribute ? $primaryVendorAttribute->getValue() : '';

        $vendorPartNumAttribute = $product->getCustomAttribute('vendor_part_num');
        $vendorPartNum = $vendorPartNumAttribute ? $vendorPartNumAttribute->getValue() : '';

        $unitOfMeasureAttribute = $product->getCustomAttribute('unit_of_measure');
        $unitOfMeasure = $unitOfMeasureAttribute ? $unitOfMeasureAttribute->getValue() : '';

        $discontinuedAttribute = $product->getCustomAttribute('discontinued');
        $discontinued = $discontinuedAttribute ? $discontinuedAttribute->getValue() : '';

        $inventoryItemAttribute = $product->getCustomAttribute('inventory_item');
        $inventoryItem = $inventoryItemAttribute ? $inventoryItemAttribute->getValue() : '';

        $discountableAttribute = $product->getCustomAttribute('discountable');
        $discountable = $discountableAttribute ? $discountableAttribute->getValue() : '';

        $tierPrices = [];
        /** @var ProductTierPriceInterface $tierPrice */
        foreach ($product->getTierPrices() as $tierPrice) {
            if (32000 == ($tierPrice->getCustomerGroupId())) {
                $customerGroupName = "E";
            } else {
                $customerGroup = $this->customerGroupRepository->getById($tierPrice->getCustomerGroupId());
                $customerGroupName = $customerGroup->getCode();
                if (strpos($customerGroupName, ' - ') !== -1) {
                    $customerGroupName = preg_replace('/ - .*/', '', $customerGroupName);
                }
            }

            if (!array_key_exists($customerGroupName, $tierPrices)) {
                $tierPrices[$customerGroupName] = [];
            }
            $tierPrices[$customerGroupName]["{$tierPrice->getQty()}"] = $tierPrice->getValue();
        }

        $productArray = $product->toFlatArray();

        $postBody = [
            'mode'                   => $product->isObjectNew() ? 'A' : 'U', // A = Add; U = Update
            'sku'                    => $product->getSku(),
            'weight'                 => $product->getWeight() ? $product->getWeight() : "",
            'taxable'                => $taxable,
            'country_of_manufacture' => $countryOfManufacture,
            'group_p_price'          => array_key_exists('P', $tierPrices) ? reset($tierPrices['P']) : '',
            'qtyPrices'              => [],
        ];

        if (array_key_exists('E', $tierPrices)) {
            foreach($tierPrices['E'] as $tQty => $tPrice) {
                $postBody['qtyPrices'][] = [
                    'group_e_qtys'   => $tQty,
                    'group_e_prices' => $tPrice,
                ];
            }
        }

        $postBody['attrValues'] = [
            [
                "attribute_key"   => "product_tagline",
                "attribute_value" => $productTagline,
            ],
            [
                "attribute_key"   => "crl_part",
                "attribute_value" => $crlPart,
            ],
            [
                "attribute_key"   => "product_group",
                "attribute_value" => $productGroup,
            ],
            [
                "attribute_key"   => "product_class",
                "attribute_value" => $productClass,
            ],
            [
                "attribute_key"   => "lead_time",
                "attribute_value" => $leadTime,
            ],
            [
                "attribute_key"   => "primary_vendor",
                "attribute_value" => $primaryVendor,
            ],
            [
                "attribute_key"   => "vendor_part_num",
                "attribute_value" => $vendorPartNum,
            ],
            [
                "attribute_key"   => "unit_of_measure",
                "attribute_value" => $unitOfMeasure,
            ],
            [
                "attribute_key"   => "cost",
                "attribute_value" => $cost,
            ],
            [
                "attribute_key"   => "price_bulk",
                "attribute_value" => array_key_exists('E', $tierPrices) ? end($tierPrices['E']) : '',
            ],
            [
                "attribute_key"   => "bulk_quantity",
                "attribute_value" => array_key_exists('E', $tierPrices) ? key($tierPrices['E']) : '',
            ],
            [
                "attribute_key"   => "price_contract",
                "attribute_value" => $product->getPrice(),
            ],
            [
                "attribute_key"   => "group_m_price",
                "attribute_value" => array_key_exists('M', $tierPrices) ? reset($tierPrices['M']) : '',
            ],
            [
                "attribute_key"   => "discontinued",
                "attribute_value" => $discontinued,
            ],
            [
                "attribute_key"   => "inventory_item",
                "attribute_value" => $inventoryItem,
            ],
            [
                "attribute_key"   => "discountable",
                "attribute_value" => $discountable
            ]
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
            // $this->logger->info(json_encode($postBody, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
            // $this->logger->info($client->getLastRequest());
            $this->logger->info(json_encode($response->getBody(), JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
            // $this->logger->info($client->getLastResponse());
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
