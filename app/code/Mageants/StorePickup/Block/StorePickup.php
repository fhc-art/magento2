<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StorePickup\Block;

/**
 * Locator Index Class
 */
class StorePickup extends \Magento\Framework\View\Element\Template
{
    /**
     * Store Collection
     *
     * @var \Mageants\StoreLocator\Model\ManageStore
     */
    protected $_storeCollection;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */    
    protected $_storeManagerInterface;
    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;
    
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Mageants\StoreLocator\Model\ManageStore
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection
    ) 
    {     
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_storeCollection = $storeCollection;
        parent::__construct($context);
    }

    public function getStoreId() 
    {
        return $this->_storeManagerInterface->getStore()->getStoreId();
    }
    
    public function isEnable() 
    {
        return $this->_scopeConfigInterface->getValue('carriers/storepickup/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public function getSelectableDateAfterDay() 
    {
        return $this->_scopeConfigInterface->getValue('carriers/storepickup/disabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public function getMinHour() 
    {
        return $this->_scopeConfigInterface->getValue('carriers/storepickup/hourMin', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }
    
    public function getMaxHour() 
    {
        return $this->_scopeConfigInterface->getValue('carriers/storepickup/hourMax', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }
    
    public function getDateFormat() 
    {
        return $this->_scopeConfigInterface->getValue('carriers/storepickup/format', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }
    
    public function getDisbleDay() 
    {
        return $this->_scopeConfigInterface->getValue('carriers/storepickup/disbleday', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }
    
    public function getStores() 
    {
        return $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')->setOrder('position','ASC');
    }    
    
}
