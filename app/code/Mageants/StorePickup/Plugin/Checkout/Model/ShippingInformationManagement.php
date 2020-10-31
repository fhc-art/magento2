<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StorePickup\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        if ($extAttributes) {
            if ($extAttributes->getPickupStore()) {
                $pickupDate = $extAttributes->getPickupDate();
                $pickupStore = $extAttributes->getPickupStore();
                $pickupStoreSelect = $extAttributes->getPickupStoreVal();
                $pickupStoreVal = 1;
                if ($pickupStoreVal) {
                    setcookie("pickupStoreVal", $pickupStoreVal, time() + (86400), "/");
                    setcookie("pickupStoreSelect", $pickupStoreSelect, time() + (86400), "/");
                }

                $quote = $this->quoteRepository->getActive($cartId);
                $quote->setPickupDate($pickupDate);
                $quote->setPickupStore($pickupStore);
                $quote->setPickupStoreVal($pickupStoreVal);
            }
        }
    }
}
