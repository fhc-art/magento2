<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Helper;

/**
 * Helper Data 
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context
     * @param \Magento\Backend\Model\UrlInterface 
     * @param \Magento\Store\Model\StoreManagerInterface 
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Mageants\StoreLocator\Model\Config\Source\StoreList $storeList,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) 
    {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->_allStoreList=$storeList;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * get products tab Url in admin
	 *
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('storelocator/storelocator/products', ['_current' => true]);
    }
	
    /**
     * Get Store Config Value
     *
	 * @return string
     */

    public function getConfigValue($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFrontName()
    {
        return $this->scopeConfig->getValue(
            "StoreLocator/general/fronturl",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

     /**
     * Get Store List
     * @return Array
     */
    public function getStoreList()
    {
     return $this->_allStoreList->toOptionArray();
    }

    public function getOpen()
    {
        $ret=array();
        $ret[0] = ['value' => '0','label' => 'No'];
        $ret[1] = ['value' => '1','label' => 'Yes'];
        return $ret;
    }

    /*public function getOTime()
    {
        $ret=array();
        for ($i=0; $i < 24 ; $i++) { 
            $ret[$i]=['value'=>$i,'label'=>$i];
        }
        return $ret;
    }*/
}
