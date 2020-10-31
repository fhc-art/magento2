<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use MageWorx\CustomerPrices\Helper\Customer as HelperCustomer;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices as ResourceCustomerPrices;

class ProductIds implements ObserverInterface
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
     * @var ResourceCustomerPrices
     */
    protected $customerPricesResourceModel;

    /**
     * ProductIds constructor.
     *
     * @param HelperCalculate $helperCalculate
     * @param HelperCustomer $helperCustomer
     * @param ResourceCustomerPrices $customerPricesResourceModel
     */
    public function __construct(
        HelperCalculate $helperCalculate,
        HelperCustomer $helperCustomer,
        ResourceCustomerPrices $customerPricesResourceModel
    ) {
        $this->helperCalculate             = $helperCalculate;
        $this->helperCustomer              = $helperCustomer;
        $this->customerPricesResourceModel = $customerPricesResourceModel;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $object     = $observer->getObject();
        $customerId = $this->helperCustomer->getCurrentCustomerId();

        if ($customerId !== null) {
            $customerPricesData = $this->customerPricesResourceModel->getDataByCustomerId($customerId);
            $linkField          = $this->helperCalculate->getLinkField();
            $productIds         = $this->helperCalculate->getColumnIds($customerPricesData, $linkField);
            $object->addData(['product_ids' => $productIds]);
        }

        return $object;
    }

}