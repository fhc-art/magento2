<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Helper\Product as HelperProduct;
use MageWorx\CustomerPrices\Model\AddCustomerPricesToBasePrice;

class ApplyCustomerPricesToCollectionObserver implements ObserverInterface
{
    /**
     * @var HelperCalculate
     */
    protected $helperCalculate;

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
     * ApplyCustomerPricesToCollectionObserver constructor.
     *
     * @param HelperCalculate $helperCalculate
     * @param HelperCustomer $helperCustomer
     * @param HelperProduct $helperProduct
     * @param AddCustomerPricesToBasePrice $customerPricesToBasePrice
     */
    public function __construct(
        HelperCalculate $helperCalculate,
        HelperCustomer $helperCustomer,
        HelperProduct $helperProduct,
        AddCustomerPricesToBasePrice $customerPricesToBasePrice
    ) {
        $this->helperCalculate           = $helperCalculate;
        $this->helperCustomer            = $helperCustomer;
        $this->helperProduct             = $helperProduct;
        $this->customerPricesToBasePrice = $customerPricesToBasePrice;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /**@var Collection $collection */
        $collection = $observer->getData('collection');
        $customerId = $this->helperCustomer->getCurrentCustomerId();

        if (!$this->helperCalculate->isCheckedCollection($customerId, $collection)) {
            return $this;
        }

        $ids = $this->getIds($collection);
        if (!empty($ids)) {
            $this->customerPricesToBasePrice->modifyCollectionPrice($collection, $ids, $customerId);
        }

        return $this;
    }

    /**
     * @param Collection $collection
     * @return array
     * @throws \Exception
     */
    protected function getIds($collection)
    {
        $ids = [];
        foreach ($collection as $product) {
            $productId       = $this->helperProduct->getProductId($product);
            $ids[$productId] = $productId;
        }

        return $ids;
    }
}