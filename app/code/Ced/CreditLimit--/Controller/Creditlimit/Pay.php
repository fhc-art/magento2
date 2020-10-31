<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CreditLimit
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CreditLimit\Controller\Creditlimit;
use Magento\Framework\Controller\ResultFactory;
class Pay extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_custmerSesion;
    /**
     * @var
     */
    protected $_resultForwardFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Magento\Catalog\Model\ProductRepository $productRepository,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Magento\Checkout\Model\Cart $cart
    ) {
        $this->_custmerSesion = $session;
        $this->_scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {   	  	
      $resultConfig = $this->getConfig('b2bextension/credit_limit/enable');
      if (!$resultConfig){
			return $this->_redirect('customer/account');
		}
		
		if (!$this->_custmerSesion->isLoggedIn()){
			return $this->_redirect ('customer/account/login');
		}
    	
    	$data = $this->getRequest()->getPostValue();	
    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    	if($data){
			
    		$minPay = $this->getConfig('b2bextension/credit_limit/min_pay_amount');
    		if ($this->getRequest()->getParam('amount')<$minPay) {
    			$this->messageManager->addErrorMessage(__('You must pay atleast %1 amount for transaction',$minPay));
    			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
    			return $resultRedirect;  			
    		}
    		if ($this->getRequest()->getParam('amount')>$data['payment_due']) {
    			$this->messageManager->addErrorMessage(__('Amount is greater than required amount'));
    			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
    			return $resultRedirect;
    		}
    		
    		try{
	    		$productobj =  $this->productRepository->get(\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU);	    		
	    		$websiteIds = $productobj->getWebsiteIds();
	    		$currentWebsite = [$this->getCurrentWebsiteId()];
	    		$newWebsiteIds = array_unique(array_merge($websiteIds,$currentWebsite));
	    		$params = ['product'=>$productobj->getId(),'qty'=>1];
	    		$productobj->setPrice($this->getRequest()->getParam('amount'));
	    		$productobj->setWebsiteIds($newWebsiteIds);
	    		$productobj->save();
	    		$this->cart->truncate();
	    		$this->cart->setCreditLimitProduct(true);
	    		$this->cart->addProduct($productobj,$params);
	    		$this->cart->save();
	    		
				return $this->_redirect('checkout');
				
    		}catch(\Exception $e){
    			$this->messageManager->addErrorMessage(__('Something Went Wrong'));
    			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
    			return $resultRedirect;
    		}
    	}
    	
    	$this->messageManager->addErrorMessage(__('Something Went Wrong'));
    	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
    	return $resultRedirect;
    }
    
    /**
     * 
     * @param string $path
     * @return \Magento\Framework\App\Config\mixed
     */
    protected function getConfig($path){
    	return $this->_scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * 
     * @return number
     */
    public function getCurrentWebsiteId(){
    	return $this->storeManager->getStore()->getWebsiteId();
    }
}
