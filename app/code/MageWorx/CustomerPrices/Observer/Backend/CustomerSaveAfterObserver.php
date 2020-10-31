<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use MageWorx\CustomerPrices\Model\Customer\PriceSaver;

class CustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * @var PriceSaver
     */
    protected $pricesSaver;

    /**
     * CustomerProductSaveAfterObserver constructor.
     *
     * @param PriceSaver $pricesSaver
     */
    public function __construct(
        PriceSaver $pricesSaver
    ) {
        $this->pricesSaver = $pricesSaver;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();

        /** @var RequestInterface $request */
        $request                 = $observer->getEvent()->getRequest();
        $customerDataFromRequest = $request->getParam('customer', []);

        if (!empty($customerDataFromRequest['entity_id'])) {
            $customerId = $customerDataFromRequest['entity_id'];
        } else {
            $customerId = $customer->getId();
        }

        if (!$customerId) {
            return $this;
        }

        $this->pricesSaver->saveCustomerPrices($customerId, $request);

        return $this;
    }
}