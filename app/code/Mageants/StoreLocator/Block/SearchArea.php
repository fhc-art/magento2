<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block;

/**
 * Locator GoogleMap 
 */
class SearchArea extends \Magento\Framework\View\Element\Template
{
	/**
	 * Current Store Collection
	 *
	 * @var \Mageants\StoreLocator\Model\ManageStore
	 */
    protected $_storeCollection;

    protected $_objectManager;

    /**
	 * File System
	 *
	 * @var \Magento\Framework\Filesystem
	 */
	protected $_filesystem ;
	
    /**
	 * Image Factory
	 *
	 * @var \Magento\Framework\Image\AdapterFactory
	 */
	protected $_imageFactory;
	
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Mageants\StoreLocator\Helper\Data
	 * @param \Mageants\StoreLocator\Model\ManageStore
	 * @param \Magento\Framework\Filesystem
	 * @param \Magento\Framework\Image\AdapterFactory
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\StoreLocator\Helper\Data $helper,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) 
    {     
        $this->_storeCollection = $storeCollection;
		$this->_helper = $helper;
		$this->_filesystem = $context->getFilesystem();               
        $this->_imageFactory = $imageFactory;  
        $this->_objectManager = $objectmanager;
        parent::__construct($context);
    }
    
	/**
	 * Prepare Layout
	 *
	 * @return Parent::_prepareLayout
	 */
    public function _prepareLayout()
    {
        //$this->pageConfig->getTitle()->set(__('Store Locator'));
        return parent::_prepareLayout();
    }

	/**
	 * Dispatch request
	 * 
	 * @param RequestInterface $request
	 * @return $dispatch
	 */
    public function dispatch(RequestInterface $request) 
    {
        return parent::dispatch($request);
    }
    
	/**
	 * Get Api key for GoogleMap
	 *
	 * @return $this
	 */
    public function getApiKey()
    {
        return $this->_scopeConfig->getValue('StoreLocator/map/map_key',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
	/**
	 * Get store market template
	 *
	 * @return $this
	 */
    public function getStoreMarkerTemplate()
    {
        return $this->_scopeConfig->getValue('StoreLocator/general/mark_template',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
	/**
	 * get Store Collection
	 *
	 * @return $collection
	 */
    public function getStoreCollection($storename,$countryid,$state,$city,$zipcode)
    {
        $storeId=$this->getStoreId();
        $storeIds=array('0' =>'0','1'=>$storeId);
        $storenameCollection="";
        $collection='';
        $collection = $this->_storeCollection->getCollection()
                    ->addFieldToFilter('sstatus', 'Enabled')
                    ->addFieldToFilter('storeId',array('in'=>$storeIds))
                    ->setOrder('position','ASC');

        if($storename!=''){
            $collection = $collection
                ->addFieldToFilter('sname', array('like' => '%'.$storename.'%'));
        }

        if($countryid!=''){
            $collection = $collection
                ->addFieldToFilter('country',$countryid);
            
        }

        if($state!=''){
            $collection = $collection
                ->addFieldToFilter('state',array('like' => '%'.$state.'%'));
        }

        if($city!=''){
            $collection = $collection
                ->addFieldToFilter('city',array('like' => '%'.$city.'%'));
        }

        if($zipcode!=''){
            $collection = $collection
                ->addFieldToFilter('postcode',array('like' => '%'.$zipcode.'%'));
        }
        return $collection;
    }
    
    public function getCountryLatLong($countryid)
    {
        $geocodeFrom=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$countryid.'&key='.$this->getApiKey());
        $outputFrom = json_decode($geocodeFrom);
        
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;

        return $latitudeFrom.",".$longitudeFrom;
    }
}
