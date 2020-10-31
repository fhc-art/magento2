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
/**
 * Class Index
 * @package Ced\CreditLimit\Controller\Creditlimit
 */
class Index extends \Magento\Framework\App\Action\Action
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
     */
    
    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_custmerSesion = $session;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
    	
      $resultConfig = $this->_scopeConfig->getValue('b2bextension/credit_limit/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
      if(!$this->_custmerSesion->isLoggedIn()){
    	return $this->_redirect('customer/account/login');
       }
       
    	if(!$this->_objectManager->get('Ced\CreditLimit\Helper\Data')->checkLimitAssign() || !$resultConfig){
    		$this->messageManager->addErrorMessage(__('Credit Account is not set.Contact Administrator'));
    		return $this->_redirect('customer/account');
    	}
    	
    	$resultPage = $this->resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->prepend(__('Credit Limit'));
    	$resultPage->getConfig()->getTitle()->prepend(__('Credit Limit'));
    	return $resultPage;
    	
    }
}
