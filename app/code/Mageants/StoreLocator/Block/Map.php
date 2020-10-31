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
class Map extends \Magento\Framework\View\Element\Template
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
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Directory\Block\Data $directoryBlock 
    ) 
    {     
        $this->_storeCollection = $storeCollection;
		$this->_helper = $helper;
		$this->_filesystem = $context->getFilesystem();               
        $this->_imageFactory = $imageFactory;  
        $this->_objectManager = $objectmanager;
        $this->directoryBlock = $directoryBlock;
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
	 * Get store location title
	 *
	 * @return $this
	 */
    public function getStoreLocatorTitle()
    {
        return $this->_scopeConfig->getValue('StoreLocator/general/title',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get store location title
     *
     * @return $this
     */
    public function getMaxRadius()
    {
        return $this->_scopeConfig->getValue('StoreLocator/general/maxradius',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
    public function getStoreCollection()
    {
        $collection = $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')
                    ->setOrder('position','ASC');;
        return $collection;
    }
    
    /**
     * get Store Collection
     *
     * @return $collection
     */
    public function getAreaStoreCollection($storename,$countryid,$state,$city,$zipcode)
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

	/**
	 * Get store collection within range
	 *
	 * @return $collection
	 */
    public function getRangeStoreCollection($current,$distance)
    {
        $StoreCollection = $this->_storeCollection->getCollection()
                        ->setOrder('position','ASC');
        $point = array();
        foreach($StoreCollection as $store)
        {
            $point1='1';
            $point1=$this->getDistance($current,$store['latitude'],$store['longitude'], "K", $distance);
            if($point1!='')
            {
                $point[]=$store['store_id'];
            }
        }
        if(sizeOf($point) > 0)
        {
			$collection = $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')
                                            ->addFieldToFilter('store_id', array('in' => array($point)))
                                            ->setOrder('position','ASC');
			return $collection;
        }
        else
        {
            return ;
        }
    }

    public function getStoreById($id)
    {
        $storeId=$this->getStoreId();
        $storeIds=array('0' =>'0','1'=>$storeId);
        $collection = $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')
                    ->addFieldToFilter('storeId',array('in'=>$storeIds))
                    ->addFieldToFilter('store_id',array('in'=>$id))
                    ->setOrder('position','ASC');
        
        return $collection;   
    }
    
    public function getStoreNameById($id)
    {
        $storeId=$this->getStoreId();
        $storeIds=array('0' =>'0','1'=>$storeId);
        $model = $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')
                    ->addFieldToFilter('storeId',array('in'=>$storeIds))
                    ->addFieldToFilter('store_id',array('in'=>$id))
                    ->setOrder('position','ASC');
        $storeurls=$model->getData();
        foreach ($storeurls as $storeurl) {
            return $storeurl['sname'];
        }
        return '';
    }
    
    public function getCountries()
    {
        $country = $this->directoryBlock->getCountryHtmlSelect();
        return $country;
    }

    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
    }

	/**
	 * return distance from current location to store location
	 *
	 * @return $km
	 */
    public function getDistance($addressFrom, $latitudeTo, $longitudeTo, $unit, $distance)
    {
        //Change address format
        $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
       
        //Send request and receive json data
        //$geocodeFrom = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false');
        $geocodeFrom=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&key='.$this->getApiKey());
        $outputFrom = json_decode($geocodeFrom);
        
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        $km=($miles * 1.609344);
        if($km<$distance)
        {
            return round($km);
        }
    }
}
