<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\StorePickup\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

class StorePickupConfigProvider implements ConfigProviderInterface
{
    const XPATH_ENABLED = 'carriers/storepickup/active';
    const XPATH_FORMAT = 'carriers/storepickup/format';
    const XPATH_DISABLED = 'carriers/storepickup/disabled';
    const XPATH_HOURMIN = 'carriers/storepickup/hourMin';
    const XPATH_HOURMAX = 'carriers/storepickup/hourMax';
    const XPATH_STORE_LOCATIONS = 'carriers/storepickup/store_locations';
    const XPATH_DISABLE_DAYS = 'carriers/storepickup/disbleday';
    const XPATH_HOLIDAYS = 'carriers/storepickup/holidays';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Store Collection
     *
     * @var \Mageants\StoreLocator\Model\ManageStore
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection,
        \Magento\Directory\Model\CountryFactory $country
    ) {
        $this->_storeCollection = $storeCollection;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->country = $country;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $storeId = $this->getStoreId();
        $enabled = $this->scopeConfig->getValue(self::XPATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
        $disabled = $this->scopeConfig->getValue(self::XPATH_DISABLED, ScopeInterface::SCOPE_STORE, $storeId);
        $hourMin = $this->scopeConfig->getValue(self::XPATH_HOURMIN, ScopeInterface::SCOPE_STORE, $storeId);
        $hourMax = $this->scopeConfig->getValue(self::XPATH_HOURMAX, ScopeInterface::SCOPE_STORE, $storeId);
        $format = $this->scopeConfig->getValue(self::XPATH_FORMAT, ScopeInterface::SCOPE_STORE, $storeId);
        $disableDays = $this->scopeConfig->getValue(self::XPATH_DISABLE_DAYS, ScopeInterface::SCOPE_STORE, $storeId);
        $holidays = $this->scopeConfig->getValue(self::XPATH_HOLIDAYS, ScopeInterface::SCOPE_STORE, $storeId);

        $collection = $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')
                    ->setOrder('position', 'ASC');
        
        $stores = '';
        foreach ($collection->getData() as $store) {
            $stores = $store;
        }
        $storeLocation = [];
        $pickupStore = [];
        if ($stores) {
            foreach ($collection->getData() as $store) {
                $countryName = $this->country->create()->loadByCode($store['country'])->getName();
                $storeLocation[] = array("name"=>$store['sname'].', '.$store['address'].', '.$store['city'].', '.$store['postcode'].', '.$store['region'].', '.$store['country'].', '.$store['phone'], "id"=> $store['store_id']);
                if (isset($store['region_id']) && isset($store['code'])) {
                    $pickupStore[$store['store_id']] = array("name" => $store['sname'], "address" => $store['address'],"city" => $store['city'],"postcode" => $store['postcode'],"region" => $store['region'],"region_id" => $store['region_id'],"code" => $store['code'],"country" => $store['country'],"phone" => $store['phone'],"countryname"=>$countryName);
                } else {
                    $pickupStore[$store['store_id']] = array("name" => $store['sname'], "address" => $store['address'],"city" => $store['city'],"postcode" => $store['postcode'],"region" => $store['region'],"country" => $store['country'],"phone" => $store['phone'],"countryname"=>$countryName);
                }
            }
        }
        
        $noday = 0;
        if ($disabled == -1) {
            $noday = 1;
        }

        $config = [
            'shipping' => [
                'store_pickup' => [
                    'enableextension' => $enabled,
                    'format' => $format,
                    'disabled' => $disabled,
                    'noday' => $noday,
                    'hourMin' => $hourMin,
                    'hourMax' => $hourMax,
                    'stores' => $storeLocation,
                    'pickupaddress' => $pickupStore,
                    'disableDays' => $disableDays,
                    'holidays'=>$holidays
                ]
            ]
        ];
        return $config;
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}
