<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\StorePickup\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveStorePickupToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * Store Collection
     *
     * @var \Mageants\StoreLocator\Model\ManageStore
     */
    protected $_storeCollection;


    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection
    ) {
        $this->_storeCollection = $storeCollection;
        $this->_objectManager = $objectmanager;
        $this->countryInformation = $countryInformation;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        $quote = $quoteRepository->get($order->getQuoteId());
        $order->setPickupDate($quote->getPickupDate());
        $order->setPickupStore($quote->getPickupStore());
        $collection = '';
        $store = '';

        if (isset($_COOKIE['pickupStoreSelect'])) {
            $id = $_COOKIE['pickupStoreSelect'];
            $collection = $this->_storeCollection->getCollection()->addFieldToFilter('store_id', $id);
        }
        if ($collection != '') {
            foreach ($collection->getData() as $stores) {
                $store = $stores;
            }
        }
        
        if ($order->getShippingMethod() == "storepickup_storepickup" && $store != '') {
            $storeName = explode(' ', $store['sname']);
            $streetAddress = array($store['address']);
            $firstName = '';
            $lastName = '';

            if (count($storeName) == 1) {
                $firstName = "Store";
                $lastName = $storeName[0];
            } elseif (count($storeName) > 2) {
                $firstName = $storeName[0];
                for ($i=1; $i < count($storeName); $i++) {
                    $lastName = $lastName." ".$storeName[$i];
                }
            } else {
                $firstName = $storeName[0];
                $lastName = $storeName[1];
            }

            if ($order->getPickupStore() == '') {
                if (isset($_COOKIE['pickupDateVal'])) {
                    $order->setPickupDate($_COOKIE['pickupDateVal']);
                }
                if (isset($_COOKIE['pickupStoreSelect'])) {
                    $order->setPickupStore($_COOKIE['pickupStoreSelect']);
                }
                if (isset($_COOKIE['pickupStoreVal'])) {
                    $order->setPickupStoreVal($_COOKIE['pickupStoreVal']);
                }
            }

            $order->getShippingAddress()->setFirstname($firstName);
            $order->getShippingAddress()->setLastname($lastName);
            $order->getShippingAddress()->setStreet($streetAddress);
            $order->getShippingAddress()->setCity($store['city']);
            $order->getShippingAddress()->setPostcode(trim($store['postcode']));
            $order->getShippingAddress()->setRegion(trim($store['region']));
            $order->getShippingAddress()->setCountryId(strtoupper(trim($store['country'])));
            $order->getShippingAddress()->setTelephone(trim($store['phone']));
            $order->getShippingAddress()->setCompany('');
        }
        return $this;
    }
}
