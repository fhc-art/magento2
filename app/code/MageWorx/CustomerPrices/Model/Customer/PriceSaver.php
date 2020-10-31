<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Model\Customer;

use Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use MageWorx\CustomerPrices\Helper\Base as HelperBase;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use MageWorx\CustomerPrices\Helper\Data as HelperData;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Model\CustomerPrices as CustomerPricesModel;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices as CustomerPricesResourceModel;
use MageWorx\CustomerPrices\Model\ResourceModel\Product\Indexer\CustomerPrice as IndexCustomPrice;
use Psr\Log\LoggerInterface;

class PriceSaver
{
    /**
     * @var CustomerPricesResourceModel
     */
    private $customerPriceResourceModel;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var HelperCalculate
     */
    private $helperCalculate;

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
     * @var HelperBase
     */
    protected $helperBase;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $prepareToReindexCustomerPrices = [];

    /**
     * @var array
     */
    protected $prepareToReindexGlobalCustomerPrices = [];

    /**
     * @var array
     */
    protected $collectedDataToSave = [];

    /**
     * @var array|null
     */
    protected $currentGlobalPriceData = null;

    /**
     * Prices constructor.
     *
     * @param CustomerPricesResourceModel $customerPriceResourceModel
     * @param HelperData $helperData
     * @param HelperBase $helperBase
     * @param HelperCalculate $helperCalculate
     * @param HelperProduct $helperProduct
     * @param IndexCustomPrice $indexer
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        CustomerPricesResourceModel $customerPriceResourceModel,
        HelperData $helperData,
        HelperBase $helperBase,
        HelperCalculate $helperCalculate,
        HelperProduct $helperProduct,
        IndexCustomPrice $indexer,
        IndexerRegistry $indexerRegistry,
        LoggerInterface $loggerInterface
    ) {
        $this->customerPriceResourceModel = $customerPriceResourceModel;
        $this->helperData                 = $helperData;
        $this->helperBase                 = $helperBase;
        $this->helperCalculate            = $helperCalculate;
        $this->helperProduct              = $helperProduct;
        $this->indexer                    = $indexer;
        $this->indexerRegistry            = $indexerRegistry;
        $this->logger                     = $loggerInterface;
    }

    /**
     * @param int $customerId
     * @param RequestInterface $request
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveCustomerPrices($customerId, $request)
    {
        $this->customerPriceResourceModel->getConnection()->beginTransaction();

        try {
            $productsPricesDataFromGrid = $this->prepareProductsPricesDataFromGrid($request);
            $productsPricesDataFromDb   = $this->prepareProductsPricesDataFromDb($customerId);

            $productsDataFromGridNeedSave   = array_diff_key($productsPricesDataFromGrid, $productsPricesDataFromDb);
            $productsDataFromGridNeedDelete = array_diff_key($productsPricesDataFromDb, $productsPricesDataFromGrid);
            $changedGlobalParams            = $this->getChangedGlobalParams($customerId, $request);

            $this->deleteIndividualConfiguredData($customerId, $productsDataFromGridNeedDelete);

            if ($changedGlobalParams) {

                $productIdsWithGlobalPrice = $this->customerPriceResourceModel->getProductIdsByCustomerIdAndPriceType(
                    $customerId,
                    CustomerPricesModel::TYPE_PRICE_СUSTOMER_GLOBAL
                );

                $this->deleteGlobalConfiguredData($customerId, $productIdsWithGlobalPrice);

                if (!empty($changedGlobalParams['price']) || !empty($changedGlobalParams['special_price'])) {
                    $productIdsWithGlobalPriceNeedSave = $this->getProductIdsWithGlobalPriceNeedSave(
                        $productsPricesDataFromGrid,
                        $productsPricesDataFromDb
                    );

                    $this->composeGlobalConfiguredData(
                        $customerId,
                        $productIdsWithGlobalPriceNeedSave,
                        $changedGlobalParams
                    );
                }
            } else {
                $this->deleteGlobalConfiguredData(
                    $customerId,
                    array_column($productsDataFromGridNeedSave, 'product_id')
                );

                if ($this->isCurrentGlobalPriceValuesExists($customerId)) {
                    $this->composeGlobalConfiguredData(
                        $customerId,
                        array_diff(
                            array_column($productsDataFromGridNeedDelete, 'product_id'),
                            array_column($productsDataFromGridNeedSave, 'product_id')
                        ),
                        $this->getCurrentGlobalPriceValues($customerId)
                    );
                }
            }

            if ($productsDataFromGridNeedSave) {
                $this->composeIndividualConfiguredData($customerId, $productsDataFromGridNeedSave);
            }

            if (!empty($this->collectedDataToSave)) {
                $this->customerPriceResourceModel->saveCustomerProductsPrices($this->collectedDataToSave);
            }

            $this->addRowsWithSpecialAttribute();
            $this->reindexCustomerPrices();

            $this->customerPriceResourceModel->commit();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->customerPriceResourceModel->rollBack();
            throw new $e;
        }
    }

    /**
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareProductsPricesDataFromDb($customerId)
    {
        $customerPricesData = $this->customerPriceResourceModel->getCustomerPricesDataFromMainTable($customerId);

        $data = [];
        foreach ($customerPricesData as $item) {
            $key        = $item['product_id'] . '|' . $item['price'] . '|' . $item['special_price'];
            $data[$key] = $item;
        }

        return $data;
    }

    /**
     * @param int $customerId
     * @param array $productsDataFromGridNeedDelete
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function deleteIndividualConfiguredData($customerId, array $productsDataFromGridNeedDelete)
    {
        if ($productsDataFromGridNeedDelete) {
            $this->deleteCustomerPrices(
                array_column($productsDataFromGridNeedDelete, 'product_id'),
                $customerId,
                CustomerPricesModel::TYPE_PRICE_CUSTOMER
            );
        }
    }

    /**
     * @param int $customerId
     * @param array $productIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function deleteGlobalConfiguredData($customerId, array $productIds)
    {
        if (!empty($productIds)) {
            $this->deleteCustomerPrices(
                $productIds,
                $customerId,
                CustomerPricesModel::TYPE_PRICE_СUSTOMER_GLOBAL
            );
        }
    }

    /**
     * @param int $customerId
     * @param array $productIds
     * @param array $prices
     * @throws \Exception
     */
    protected function composeGlobalConfiguredData($customerId, $productIds, $prices)
    {
        if ($productIds) {
            $linkProductIdsToTypeId = $this->customerPriceResourceModel->getLinkProductIdsToTypeId(
                $productIds
            );

            foreach ($productIds as $productId) {
                $this->prepareToSave(
                    $customerId,
                    $productId,
                    $prices['price'] ?? '',
                    $prices['special_price'] ?? '',
                    CustomerPricesModel::TYPE_PRICE_СUSTOMER_GLOBAL
                );

                if (empty($linkProductIdsToTypeId[$productId])) {
                    continue;
                }
                $this->prepareToReindex($productId, $customerId, $linkProductIdsToTypeId[$productId]);
            }
        }
    }

    /**
     * @param int $customerId
     * @param array $productData
     * @throws \Exception
     */
    protected function composeIndividualConfiguredData($customerId, $productData)
    {
        if ($productData) {

            $linkProductIdsToTypeId = $this->customerPriceResourceModel->getLinkProductIdsToTypeId(
                array_column($productData, 'product_id')
            );

            foreach ($productData as $datum) {

                $this->prepareToSave(
                    $customerId,
                    $datum['product_id'],
                    $datum['price'],
                    $datum['special_price'],
                    CustomerPricesModel::TYPE_PRICE_CUSTOMER
                );

                if (empty($linkProductIdsToTypeId[$datum['product_id']])) {
                    continue;
                }

                $this->prepareToReindex(
                    $datum['product_id'],
                    $customerId,
                    $linkProductIdsToTypeId[$datum['product_id']]
                );
            }
        }
    }

    /**
     * @param array $productsPricesDataFromGrid
     * @param array $productsPricesDataFromDb
     * @return array
     * @throws \Exception
     */
    protected function getProductIdsWithGlobalPriceNeedSave($productsPricesDataFromGrid, $productsPricesDataFromDb)
    {
        $productsDataFromGridNeedSave = array_diff_key(
            $productsPricesDataFromGrid,
            $productsPricesDataFromDb
        );

        $productsDataNonChangable = array_intersect_key(
            $productsPricesDataFromGrid,
            $productsPricesDataFromDb
        );

        $individualConfiguredProductData = array_merge(
            $productsDataFromGridNeedSave,
            $productsDataNonChangable
        );

        $allProductIds = $this->customerPriceResourceModel->getAllProductIdsByAllowedProductTypes(
            $this->helperBase->getAllowedProductTypes()
        );

        $productIdsWithGlobalPriceNeedSave = array_diff(
            $allProductIds,
            array_column($individualConfiguredProductData, 'product_id')
        );

        return $productIdsWithGlobalPriceNeedSave;
    }

    /**
     * If global product price params wasn't changed the empty array will be retrieved
     *
     * @param $customerId
     * @param $request
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getChangedGlobalParams($customerId, $request)
    {
        $globalProductPriceData = $request->getParam('customerprices_global_price');

        $requestPrices['price']         = $globalProductPriceData['global_price'];
        $requestPrices['special_price'] = $globalProductPriceData['global_special_price'];

        $currentPrices = $this->getCurrentGlobalPriceValues($customerId);

        $modifiedPrices = array_diff_assoc($requestPrices, $currentPrices);

        if (!$modifiedPrices) {
            return $modifiedPrices;
        }

        $modifiedPrices['price']         = $modifiedPrices['price'] ?? $globalProductPriceData['global_price'];
        $modifiedPrices['special_price'] = $modifiedPrices['special_price'] ?? $globalProductPriceData['global_special_price'];

        return $modifiedPrices;
    }

    /**
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCurrentGlobalPriceValues($customerId)
    {
        if (is_null($customerId)) {
            return [];
        }

        if ($this->currentGlobalPriceData === null) {
            $customerGlobalPriceData = $this->customerPriceResourceModel->getProductDataByCustomerIdAndTypePrice(
                $customerId,
                CustomerPricesModel::TYPE_PRICE_СUSTOMER_GLOBAL
            );

            $prices['price']         = !empty($customerGlobalPriceData['price']) ? $customerGlobalPriceData['price'] : '';
            $prices['special_price'] = !empty($customerGlobalPriceData['special_price']) ? $customerGlobalPriceData['special_price'] : '';

            $this->currentGlobalPriceData = $prices;
        }

        return $this->currentGlobalPriceData;
    }

    /**
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isCurrentGlobalPriceValuesExists($customerId)
    {
        return (bool)array_filter($this->getCurrentGlobalPriceValues($customerId));
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @param string $price
     * @param string $specialPrice
     * @param int $typePrice
     */
    protected function prepareToSave($customerId, $productId, $price, $specialPrice, $typePrice)
    {
        $priceType        = $this->helperCalculate->getPriceType($price);
        $specialPriceType = $this->helperCalculate->getPriceType($specialPrice);

        $priceSign        = $this->helperCalculate->getPriceSign($price);
        $specialPriceSign = $this->helperCalculate->getPriceSign($specialPrice);

        $priceValue        = $this->getAbsPriceValue($price);
        $specialPriceValue = $this->getAbsPriceValue($specialPrice);

        $this->collectedDataToSave[] = [
            'attribute_type'      => $typePrice,
            'customer_id'         => $customerId,
            'product_id'          => $productId,
            'price'               => $price,
            'price_type'          => $priceType,
            'special_price'       => $specialPrice,
            'special_price_type'  => $specialPriceType,
            'discount'            => null,
            'discount_price_type' => 1,
            'price_sign'          => $priceSign,
            'price_value'         => $priceValue,
            'special_price_sign'  => $specialPriceSign,
            'special_price_value' => $specialPriceValue
        ];
    }

    /**
     * @param int $productId
     * @param int $customerId
     * @param string $productType
     */
    protected function prepareToReindex($productId, $customerId, $productType)
    {
        $this->prepareToReindexCustomerPrices[$productType]['product_id'][$productId]   = $productId;
        $this->prepareToReindexCustomerPrices[$productType]['customer_id'][$customerId] = $customerId;
        $this->prepareToReindexCustomerPrices[$productType]['product_type']             = $productType;
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Exception
     */
    protected function prepareProductsPricesDataFromGrid($request)
    {
        $data             = [];
        $productPriceData = $request->getParam('select_products_price');
        if (is_null($productPriceData)) {
            return $data;
        }

        $validProductsPriceData = [];
        $productsPriceData      = json_decode($productPriceData, true);

        foreach ($productsPriceData as $productId => $item) {
            if (empty($productId) || (empty($item['price']) && empty($item['special_price']))) {
                continue;
            }

            $validProductsPriceData[$productId] = $item;
        }

        if ($validProductsPriceData) {

            $productIdsPairs = $this->helperProduct->getProductIdsPairs(array_keys($validProductsPriceData));

            foreach ($validProductsPriceData as $productId => $item) {

                $linkFieldId = array_search($productId, $productIdsPairs);

                if (!$linkFieldId) {
                    continue;
                }

                $key = $linkFieldId . '|' . $item['price'] . '|' . $item['special_price'];

                $data[$key]['product_id']    = $linkFieldId;
                $data[$key]['price']         = $item['price'];
                $data[$key]['special_price'] = $item['special_price'];
            }
        }

        return $data;
    }

    /**
     * Srt to float and abs
     *
     * @param $strPrice
     * @return float|int|null
     */
    protected function getAbsPriceValue($strPrice)
    {
        if ($strPrice == '') {
            return null;
        }

        return abs(floatval($strPrice));
    }

    /**
     * @param array $productIds
     * @param int $customerId
     * @param int $typePrice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function deleteCustomerPrices($productIds, $customerId, $typePrice)
    {
        $this->customerPriceResourceModel->deleteProductsFromTableMageworxCustomerGroupPrices(
            $productIds,
            $customerId,
            $typePrice
        );

        $this->customerPriceResourceModel->deleteRowsFromTableMageworxCatalogProductEntityDecimalCustomerPrices(
            $productIds,
            $customerId
        );

        $entityIds = $this->helperProduct->getProductsEntityIds($productIds);
        $this->customerPriceResourceModel->deleteRowsFromTableMageworxCatalogProductIndexPrice(
            $entityIds,
            $customerId
        );
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addRowsWithSpecialAttribute()
    {
        $ids = $this->collectedDataToSave ? array_column($this->collectedDataToSave, 'product_id') : [];

        if ($ids) {
            $productIdsWithoutSpecialAttribute = $this->getProductIdsWithoutSpecialAttribute($ids);

            /* set data in catalog_product_entity_decimal */
            if (!empty($productIdsWithoutSpecialAttribute)) {
                $this->customerPriceResourceModel->addRowsWithSpecialAttribute($productIdsWithoutSpecialAttribute);
            }
        }
    }

    /**
     * @param array $productIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getProductIdsWithoutSpecialAttribute($productIds)
    {
        $productIdsWithSpecialAttribute = $this->customerPriceResourceModel->getProductIdsWithSpecialAttribute(
            $productIds
        );

        $productIdsWithoutSpecialAttribute = array_diff(
            $productIds,
            $productIdsWithSpecialAttribute
        );

        return $productIdsWithoutSpecialAttribute;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexCustomerPrices()
    {
        /* reindex data */
        foreach ($this->prepareToReindexCustomerPrices as $reindexData) {
            $this->indexer->setTypeId($reindexData['product_type']);
            $this->indexer->reindexEntityCustomer($reindexData['product_id'], $reindexData['customer_id']);
        }

        /* add notification need reindex catalogrule_rule */
        if ($this->helperData->isEnabledCustomerPriceInCatalogPriceRule()
            && (!empty($this->prepareToReindexCustomerPrices) || !empty($this->prepareToReindexGlobalCustomerPrices))) {
            $this->indexerRegistry->get(RuleProductProcessor::INDEXER_ID)->invalidate();
        }
    }
}