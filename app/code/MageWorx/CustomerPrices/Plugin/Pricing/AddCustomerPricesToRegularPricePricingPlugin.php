<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Plugin\Pricing;

use Magento\Catalog\Pricing\Price\RegularPrice;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Model\AddCustomerPricesToBasePrice;

class AddCustomerPricesToRegularPricePricingPlugin
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
     * AddCustomerPricesToRegularPricePricingPlugin constructor.
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
     * @param RegularPrice $subject
     * @param bool|float $result
     * @return float|null
     * @throws \Exception
     */
    public function afterGetValue(RegularPrice $subject, $result)
    {
        $customerId = $this->helperCustomer->getCurrentCustomerId();
        if (!$customerId) {
            return $result;
        }

        $product      = $subject->getProduct();
        $regularPrice = $this->customerPricesToBasePrice->modifyProductRegularPrice($product, $customerId);

        if (is_null($regularPrice)) {
            return $result;
        }

        return $regularPrice;
    }
}