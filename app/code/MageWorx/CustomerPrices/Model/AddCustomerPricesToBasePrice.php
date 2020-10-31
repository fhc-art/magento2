<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Model;

use Magento\Catalog\Model\Product;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices as ResourceCustomerPrices;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class AddCustomerPricesToBasePrice
{
    /**
     * @var HelperProduct
     */
    protected $helperProduct;

    /**
     * @var HelperCalculate
     */
    protected $helperCalculate;

    /**
     * @var ResourceCustomerPrices
     */
    protected $customerPricesResourceModel;

    /**
     * @var array
     */
    protected $cachePricesData;

    /**
     * AddCustomerPricesToBasePrice constructor.
     *
     * @param HelperProduct $helperProduct
     * @param HelperCalculate $helperCalculate
     * @param ResourceCustomerPrices $customerPricesResourceModel
     */
    public function __construct(
        HelperProduct $helperProduct,
        HelperCalculate $helperCalculate,
        ResourceCustomerPrices $customerPricesResourceModel
    ) {
        $this->helperProduct               = $helperProduct;
        $this->helperCalculate             = $helperCalculate;
        $this->customerPricesResourceModel = $customerPricesResourceModel;
    }

    /**
     * @param Collection $collection
     * @param array $ids
     * @param int $customerId
     * @throws \Exception
     */
    public function modifyCollectionPrice($collection, $ids, $customerId)
    {
        $calculatedProductsPricesByCollectionIds = $this->customerPricesResourceModel->getCalculatedProductsDataByCustomer(
            $ids,
            $customerId
        );

        if (!empty($calculatedProductsPricesByCollectionIds)) {
            $priceAttributeId        = $this->customerPricesResourceModel->getPriceAttributeId();
            $specialPriceAttributeId = $this->customerPricesResourceModel->getSpecialPriceAttributeId();
            $linkField               = $this->helperCalculate->getLinkField();
        }

        foreach ($collection as $product) {
            $productId = $this->helperProduct->getProductId($product);
            if (!empty($this->cachePricesData[$productId][$customerId]['price']) || is_null($product->getPrice())) {
                continue;
            }

            foreach ($calculatedProductsPricesByCollectionIds as $productPrice) {
                if ($productId != $productPrice[$linkField]) {
                    continue;
                }
                if (isset($productPrice['value']) && $productPrice['value'] >= 0) {
                    if ($priceAttributeId == $productPrice['attribute_id']) {
                        $this->cachePricesData[$productId][$customerId]['price'] = (float)$productPrice['value'];
                    }

                    if ($specialPriceAttributeId == $productPrice['attribute_id']
                        && $product->getPrice() > $productPrice['value'])
                    {
                        $this->cachePricesData[$productId][$customerId]['price'] = (float)$productPrice['value'];;
                        $this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'] = true;
                    }
                }
            }
        }

        foreach ($collection as $product) {
            $this->modifyProductPrice($product, $customerId);
            $this->modifyProductRegularPrice($product, $customerId);
            $this->modifyProductSpecialPrice($product, $customerId);
        }
    }

    /**
     * @param Product $product
     * @param int $customerId
     * @return float|null
     * @throws \Exception
     */
    public function modifyProductPrice($product, $customerId)
    {
        if (is_null($product->getPrice())) {
            return null;
        }
        $productId = $this->helperProduct->getProductId($product);
        if (!isset($this->cachePricesData[$productId][$customerId]['price'])) {
            $this->cachePricesData[$productId][$customerId]['price']                         = null;
            $this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'] = false;

            $calculatedCustomerProductPrices = $this->customerPricesResourceModel->getCalculatedProductDataByCustomer(
                $productId,
                $customerId
            );

            if (!empty($calculatedCustomerProductPrices)) {
                $priceAttributeId        = $this->customerPricesResourceModel->getPriceAttributeId();
                $specialPriceAttributeId = $this->customerPricesResourceModel->getSpecialPriceAttributeId();
            }

            foreach ($calculatedCustomerProductPrices as $productPrice) {
                if (isset($productPrice['value']) && $productPrice['value'] >= 0) {
                    if ($priceAttributeId == $productPrice['attribute_id']) {
                        $price = (float)$productPrice['value'];
                        $product->setData('price', $price);
                        $this->cachePricesData[$productId][$customerId]['price'] = $price;
                    }

                    if ($specialPriceAttributeId == $productPrice['attribute_id']
                        && $product->getPrice() > $productPrice['value'])
                    {
                        $price = (float)$productPrice['value'];
                        $product->setData('special_price', $price);
                        $this->cachePricesData[$productId][$customerId]['price']                         = $price;
                        $this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'] = true;
                    }
                }
            }
        }

        return $this->cachePricesData[$productId][$customerId]['price'];
    }

    /**
     * @param Product $product
     * @param int $customerId
     * @return float|null
     * @throws \Exception
     */
    public function modifyProductRegularPrice($product, $customerId)
    {
        $productId = $this->helperProduct->getProductId($product);
        if (!isset($this->cachePricesData[$productId][$customerId]['regular_price'])) {
            $this->cachePricesData[$productId][$customerId]['regular_price'] = null;

            $calculatedCustomerProductPrices = $this->customerPricesResourceModel->getCalculatedProductDataByCustomer(
                $productId,
                $customerId
            );

            if (!empty($calculatedCustomerProductPrices)) {
                $priceAttributeId = $this->customerPricesResourceModel->getPriceAttributeId();
            }

            foreach ($calculatedCustomerProductPrices as $productPrice) {
                if (isset($productPrice['value']) && $productPrice['value'] >= 0
                    && $priceAttributeId == $productPrice['attribute_id'])
                {
                    $regularPrice = (float)$productPrice['value'];
                    $product->setData('price', $regularPrice);
                    $this->cachePricesData[$productId][$customerId]['regular_price'] = $regularPrice;

                }
            }
        }

        return $this->cachePricesData[$productId][$customerId]['regular_price'];
    }

    /**
     * @param Product $product
     * @param int $customerId
     * @return float|null
     * @throws \Exception
     */
    public function modifyProductSpecialPrice($product, $customerId)
    {
        if (is_null($product->getPrice())) {
            return null;
        }
        $productId = $this->helperProduct->getProductId($product);
        if (!isset($this->cachePricesData[$productId][$customerId]['special_price'])) {
            $this->cachePricesData[$productId][$customerId]['special_price']                 = null;
            $this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'] = false;

            $calculatedCustomerProductPrices = $this->customerPricesResourceModel->getCalculatedProductDataByCustomer(
                $productId,
                $customerId
            );

            if (!empty($calculatedCustomerProductPrices)) {
                $specialPriceAttributeId = $this->customerPricesResourceModel->getSpecialPriceAttributeId();
            }

            foreach ($calculatedCustomerProductPrices as $productPrice) {
                if (isset($productPrice['value']) && $productPrice['value'] >= 0) {
                    if ($specialPriceAttributeId == $productPrice['attribute_id']
                        && $product->getPrice() > $productPrice['value'])
                    {
                        $specialPrice = (float)$productPrice['value'];
                        $product->setData('special_price', $specialPrice);
                        $this->cachePricesData[$productId][$customerId]['special_price']                 = $specialPrice;
                        $this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'] = true;
                    }
                }
            }
        }

        return $this->cachePricesData[$productId][$customerId]['special_price'];
    }

    /**
     * @param int $productId
     * @param int $customerId
     * @return bool
     * @throws \Exception
     */
    public function hasProductSetCustomerSpecialPrice($productId, $customerId)
    {
        return empty($this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'])
            ? false : $this->cachePricesData[$productId][$customerId]['is_set_customer_special_price'];
    }
}