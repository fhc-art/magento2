<?php
namespace Mageants\StoreLocator\Controller;
 
/**
 * Inchoo Custom router Controller Router
 *
 * @author      Zoran Salamun <zoran.salamun@inchoo.net>
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;
 
    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;
 
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }
 
    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');;
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $_objectManager->create('Mageants\StoreLocator\Model\ManageStore');
        $storeurls=$model->getCollection()->addFieldToSelect('storeurl')->getData();

        $route = $_objectManager->create('Mageants\StoreLocator\Helper\Data')->getConfigValue('StoreLocator/general/fronturl');
        $identifierUrl = str_replace($route."/","",trim($request->getPathInfo(), '/'));

        if (str_replace("/","",$request->getRequestString())== $route) {
            $request->setModuleName('storelocator')
                ->setControllerName('index')
                ->setActionName('index');
        } elseif ($return_val=$this->getStoreUrls($identifierUrl)) {
            $request->setModuleName('storelocator')
                ->setControllerName('store')
                ->setActionName('store')
                ->setParam('id',$return_val);
            
        }
        else {return false;}

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

    public function getStoreUrls($identifier)
    {   
        
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $model = $_objectManager->create('Mageants\StoreLocator\Model\ManageStore');
        $storeurls=$model->getCollection()->getData();
        foreach ($storeurls as $storeurl) {
            //echo stripslashes(str_replace (".html", "", $identifier));
            $identifier= stripslashes(str_replace(".html", "", $identifier));
            if (str_replace ("/", "", $identifier)== $storeurl['storeurl']) {
                return $storeurl['store_id'];
            }
        }

    }
}