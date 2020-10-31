<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block;

/**
 * Locator Index Class
 */
class Index extends \Magento\Framework\View\Element\Template
{
	/**
	 * Store Collection
	 *
	 * @var \Mageants\StoreLocator\Model\ManageStore
	 */
    protected $_storeCollection;
    
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Mageants\StoreLocator\Helper\Data
	 * @param \Mageants\StoreLocator\Model\ManageStore
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\StoreLocator\Helper\Data $helper,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection
    ) 
    {     
        $this->_storeCollection = $storeCollection;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    
	/**
	 * Prepare Layout
	 *
	 * @return $this
	 */
    public function _prepareLayout()
    {
		$this->pageConfig->getTitle()->set(__('Store Locator'));	
		if ($this->getStoreCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'store_pager'
            )->setAvailableLimit(array(4=>4,8=>8,12=>12))
                ->setShowPerPage(true)->setCollection(
                    $this->getStoreCollection()
                );
				$pager->setShowAmounts(true);
            $this->setChild('store_pager', $pager);
            $this->getStoreCollection()->load();
        }
        return $this;
    }
	
	/**
	 * Check Dispatch
	 *
	 * @param RequestInterface $request
	 * @return $dispatch
	 */
    public function dispatch(RequestInterface $request) 
    {
        return parent::dispatch($request);
    }
    
	/**
	 * get Api key for Google Map
	 *
	 * @return $this
	 */
    public function getApiKey()
    {
        return $this->_scopeConfig->getValue('StoreLocator/map/map_key');
    }
    
	/**
	 * get store Collection
	 *
	 * @return $collection
	 */
    public function getStoreCollection()
    { 
        $collection = $this->_storeCollection->getCollection()->addFieldToFilter('sstatus', 'Enabled')
                    ->setOrder('position','ASC');
        
        return $collection;
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
    /**
	 * get pager for store list in left side
	 *
	 * @return $this
	 */
    public function getPagerHtml()
    {
        return $this->getChildHtml('store_pager');    
    }
    
	/**
	 * get Distance of store from current Location
	 * @param $addressFrom
	 * @param $addressTo
	 * @param $unit
	 * @param $distance
	 * @return $km
	 */
    public function getDistance($addressFrom, $addressTo,$unit,$distance)
    {
        //Change address format
        $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
        $formattedAddrTo = str_replace(' ', '+', $addressTo);
        
        //Send request and receive json data
        $geocodeFrom = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false');
        $outputFrom = json_decode($geocodeFrom);
        $geocodeTo = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrTo.'&sensor=false');
        $outputTo = json_decode($geocodeTo);
        
        //Get latitude and longitude from geo data
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        $latitudeTo = $outputTo->results[0]->geometry->location->lat;
        $longitudeTo = $outputTo->results[0]->geometry->location->lng;
        
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
            return $km;
        }
    }
}
