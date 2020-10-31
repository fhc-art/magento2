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

namespace Ced\CreditLimit\Controller\Adminhtml\Climit;
 use Magento\Framework\Controller\ResultFactory;

 /**
  * Class Save
  * @package Ced\CreditLimit\Controller\Adminhtml\Climit
  */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
        protected $resultPageFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
        public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Ced\CreditLimit\Model\CreditLimit $creditLimit
        )
        {
            parent::__construct($context);
            $this->resultPageFactory = $resultPageFactory;
            $this->creditLimit = $creditLimit;
        }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();
    	
    	$resultRedirect = $this->resultRedirectFactory->create();
    	if($data){
	      if(!isset($data['customer_id'])){
	    	    $this->messageManager->addError(__('Please Select Customer'));
	    	    $resultRedirects = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	    		$resultRedirects->setUrl($this->_redirect->getRefererUrl());
	    		return $resultRedirects;
	    	}
    		try {

      		if($this->getRequest()->getParam('id')){
                $model = $this->getModel();
      			$model->load($this->getRequest()->getParam('id'));
      			if($data['credit_amount']<$model->getCreditAmount()){
      				$this->messageManager->addErrorMessage(__('Total limit must be greater the existing limit'));
      				$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
      				if ($this->getRequest()->getParam('back')) {
      					return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
      				}
      				return $resultRedirect->setPath('*/*/');
      			}
      			$previousamount = $model->getCreditAmount();
      			$newAmount = $data['credit_amount'] - $previousamount;
      			if($newAmount>0){
      				$remainingamount = $model->getRemainingAmount();
      				$newremainingamount =  $newAmount + $remainingamount;
      				$model->setCreditAmount($data['credit_amount']);
      				$model->setRemainingAmount($newremainingamount);
      				$model->save();
      			}
      			
      		}
      		else{
      			if(is_array($data['customer_id']) && count($data['customer_id'])>0){
              foreach($data['customer_id'] as $customerId){
              	if(is_numeric($customerId)){
	                $model = $this->getModel();
	                $customer = $this->_objectManager->create("Magento\Customer\Model\Customer")->load($customerId);
	                $model->addData($data);
	                $model->setCustomerId($customerId);
	                $model->setCustomerEmail($customer->getEmail());
	                $model->setUsedAmount(0.00);
	                $model->setRemainingAmount($data['credit_amount']);
	                $model->save();
              	}
              }
              $this->messageManager->addSuccess(__('Credit Limit Created Successfully'));
              return $resultRedirect->setPath('*/*/');
            }else{
                 $model = $this->getModel();
                 $customer = $this->_objectManager->create("Magento\Customer\Model\Customer")->load($data['customer_id']);
                 $model->addData($data);
                 $model->setCustomerEmail($customer->getEmail());
                 $model->setUsedAmount(0.00);
                 $model->setRemainingAmount($data['credit_amount']);
                 $model->save();
            }
      			
      	}
      		$this->messageManager->addSuccess(__('Credit Limit Created Successfully'));
      		$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
      		if ($this->getRequest()->getParam('back')) {
      			return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
      		}
      		return $resultRedirect->setPath('*/*/');
    	} catch (\Magento\Framework\Exception\LocalizedException $e) {
    		$this->messageManager->addError($e->getMessage());
    	} catch (\RuntimeException $e) {
    		$this->messageManager->addError($e->getMessage());
    	} catch (\Exception $e) {
    		$this->messageManager->addException($e, __('Something went wrong while editing the request.'));
    	}
    	
    		$this->_getSession()->setFormData($this->getRequest()->getPostValue());
    		return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
    	}
    	else{
    		$this->messageManager->addError(__('No Data To Save'));
    	}
    	return $resultRedirect->setPath('*/*/');
    }

    protected function getModel(){
    	
      return $this->_objectManager->create("Ced\CreditLimit\Model\CreditLimit");
      
    }
}
