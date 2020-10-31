<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageants\StorePickup\Controller\Checkout;

use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ShippingPost extends \Magento\Multishipping\Controller\Checkout\ShippingPost
{
    /**
     * @return void
     */

    /**
     * @var \Mageants\StoreLocator\Model\ManageStore
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Directory\Model\Region 
     */
    protected $_regionDataCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Directory\Model\Region $regionDataCollection,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection
    ) {        
        $this->_regionDataCollection = $regionDataCollection;
        $this->_storeCollection = $storeCollection;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );
    }

    public function execute()
    {
        $shippingMethods = $this->getRequest()->getPost('shipping_method');
        $pickupStore = $this->getRequest()->getPost('pickup_store');
        $pickupDate = $this->getRequest()->getPost('pickup_date');

        try {
            $this->_eventManager->dispatch(
                'checkout_controller_multishipping_shipping_post',
                ['request' => $this->getRequest(), 'quote' => $this->_getCheckout()->getQuote()]
            );
            $this->_getCheckout()->setShippingMethods($shippingMethods);

            $this->setStorePickupQuote($shippingMethods, $pickupStore, $pickupDate);
            $this->_getState()->setActiveStep(State::STEP_BILLING);
            $this->_getState()->setCompleteStep(State::STEP_SHIPPING);
            $this->_redirect('*/*/billing');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/shipping');
        }
    }

    public function setStorePickupQuote($shippingMethods, $pickupStore, $pickupDate){        
        if(count($pickupStore)>0){
            foreach($pickupStore as $key=>$value){
                $collection = $this->_storeCollection->getCollection()->addFieldToFilter('store_id', $value);
                $store = array();
                foreach($collection->getData() as $stores) 
                {
                    $store = $stores;
                }
                if (isset($store['sname'])) { 
                    $storeName = explode(' ', trim($store['sname']));
                    $streetAddress = $store['address'];
                    $firstName = '';
                    $lastName = '';
                    if (count($storeName) == 1) 
                    {
                        $firstName = "Store";
                        $lastName = $storeName[0];
                    }
                    elseif (count($storeName) > 2) 
                    {
                        $firstName = $storeName[0];
                        for ($i=1; $i < count($storeName); $i++) 
                        { 
                            $lastName = $lastName." ".$storeName[$i];
                        }
                    }
                    else{
                        $firstName = $storeName[0];
                        $lastName = $storeName[1];
                    }

                    $region = $this->_regionDataCollection->loadByName($store['region'],trim($store['country']));

                    $quoteAddress = $this->_getCheckout()->getCheckoutSession()->getQuote()->getAddressById($key);
                    $pickstore = $firstName." ".$lastName;
                    $quoteAddress->setShippingMethods($shippingMethods[$key]);
                    $quoteAddress->setPickupDate($pickupDate[$key]);
                    $quoteAddress->setPickupStore($pickstore);
                    $quoteAddress->setCustomerAddressId(NULL);
                    $quoteAddress->setFirstname($firstName);
                    $quoteAddress->setLastname($lastName);
                    $quoteAddress->setStreet($streetAddress);
                    $quoteAddress->setCity($store['city']);
                    $quoteAddress->setPostcode(trim($store['postcode']));
                    $quoteAddress->setRegion(trim($store['region']));
                    $quoteAddress->setRegionId($region->getRegionId());
                    $quoteAddress->setCountryId(strtoupper(trim($store['country'])));
                    $quoteAddress->setTelephone(trim($store['phone']));
                    $quoteAddress->setCompany('');
                    $quoteAddress->save();
                }
            }
        } 
        return;
    }
}
