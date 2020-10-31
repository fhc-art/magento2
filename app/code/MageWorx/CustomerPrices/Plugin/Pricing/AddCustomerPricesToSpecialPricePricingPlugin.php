<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Plugin\Pricing;

use Magento\Catalog\Pricing\Price\SpecialPrice;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Helper\Data as HelperData;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Model\AddCustomerPricesToBasePrice;

class AddCustomerPricesToSpecialPricePricingPlugin
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
     * AddCustomerPricesToSpecialPricePricingPlugin constructor.
     *
     * @param HelperData $helperData
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
     * @param SpecialPrice $subject
     * @param bool|float $result
     * @return float|null
     * @throws \Exception
     */
    public function afterGetValue(SpecialPrice $subject, $result)
    {
        $customerId = $this->helperCustomer->getCurrentCustomerId();
        if (!$customerId) {
            return $result;
        }

        $product      = $subject->getProduct();
        $productId    = $this->helperProduct->getProductId($product);
        $specialPrice = $this->customerPricesToBasePrice->modifyProductSpecialPrice($product, $customerId);

        if (is_null($specialPrice)) {
            return $result;
        }

        if (!$this->customerPricesToBasePrice->hasProductSetCustomerSpecialPrice($productId, $customerId)) {
            return $result;
        }

        return $specialPrice;
    }
}