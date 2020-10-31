<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Plugin;

use Magento\Catalog\Model\Product\Type\Price;
use MageWorx\CustomerPrices\Model\AddCustomerPricesToBasePrice;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use Magento\Catalog\Model\Product;

class AddCustomerPricesToBasePricePlugin
{
    /**
     * @var HelperCustomer
     */
    protected $helperCustomer;

    /**
     * @var HelperProduct
     */
    protected $helperProduct;

    /**
     * @var AddCustomerPricesToBasePrice
     */
    protected $customerPricesToBasePrice;

    /**
     * AddCustomerPricesToBasePricePlugin constructor.
     *
     * @param HelperCustomer $helperCustomer
     * @param HelperProduct $helperProduct
     * @param AddCustomerPricesToBasePrice $customerPricesToBasePrice
     */
    public function __construct(
        HelperCustomer $helperCustomer,
        HelperProduct $helperProduct,
        AddCustomerPricesToBasePrice $customerPricesToBasePrice
    ) {
        $this->helperCustomer            = $helperCustomer;
        $this->helperProduct             = $helperProduct;
        $this->customerPricesToBasePrice = $customerPricesToBasePrice;
    }

    /**
     * @param Price $subject
     * @param callable $proceed
     * @param Product $product
     * @param null $qty
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetBasePrice(Price $subject, callable $proceed, $product, $qty = null)
    {
        $customerId = $this->helperCustomer->getCurrentCustomerId();
        if (!$customerId) {
            return $proceed($product, $qty);
        }

        $productId  = $this->helperProduct->getProductId($product);
        $price = $this->customerPricesToBasePrice->modifyProductPrice($product, $customerId);

        if (is_null($price)) {
            return $proceed($product, $qty);
        }

        if ($this->helperProduct->hasProductTierPrice($product, $qty)) {
            return $proceed($product, $qty);
        }

        if (!$this->customerPricesToBasePrice->hasProductSetCustomerSpecialPrice($productId,$customerId)){
            return $proceed($product, $qty);
        }

        return $price;
    }
}