<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Plugin\Price;

use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use Magento\Catalog\Model\ResourceModel\Product\Price\SpecialPrice as CatalogSpecialPrice;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices as ResourceCustomerPrices;

class SpecialPrice
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
     * @var HelperCalculate
     */
    protected $helperCalculate;

    /**
     * @var ResourceCustomerPrices
     */
    protected $customerPricesResourceModel;

    /**
     * SpecialPrice constructor.
     *
     * @param HelperCustomer $helperCustomer
     * @param HelperProduct $helperProduct
     * @param HelperCalculate $helperCalculate
     * @param ResourceCustomerPrices $customerPricesResourceModel
     */
    public function __construct(
        HelperCustomer $helperCustomer,
        HelperProduct $helperProduct,
        HelperCalculate $helperCalculate,
        ResourceCustomerPrices $customerPricesResourceModel
    ) {
        $this->helperCustomer              = $helperCustomer;
        $this->helperProduct               = $helperProduct;
        $this->helperCalculate             = $helperCalculate;
        $this->customerPricesResourceModel = $customerPricesResourceModel;
    }

    /**
     * @param CatalogSpecialPrice $subject
     * @param array $result
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGet(CatalogSpecialPrice $subject, $result)
    {
        $customerId = $this->helperCustomer->getCurrentCustomerId();
        if (!$customerId) {
            return $result;
        }
        $linkFiled  = $this->helperCalculate->getLinkField();
        $productIds = $this->getIds($result, $linkFiled);

        $calculatedProductSpecialPrices = $this->customerPricesResourceModel->getCalculatedProductsSpecialPricesByCustomer(
            $productIds,
            $customerId
        );

        foreach ($result as &$item) {
            $productId = $item[$linkFiled];
            if (!empty($calculatedProductSpecialPrices[$productId])) {
                $item['value'] = $calculatedProductSpecialPrices[$productId]['value'];
            }
        }

        return $result;
    }

    /**
     * @param array $specialPriceData
     * @param string $linkFiled
     * @return array
     */
    protected function getIds($specialPriceData, $linkFiled)
    {
        $ids = [];
        foreach ($specialPriceData as $item) {
            $ids[$item[$linkFiled]] = $item[$linkFiled];
        }

        return $ids;
    }
}