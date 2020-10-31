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
/**
 * Class SavePayment
 * @package Ced\CreditLimit\Controller\Adminhtml\Climit
 */
class SavePayment extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
        protected $resultPageFactory;

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ced\CreditLimit\Helper\Data $helper
     * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
     */
        public function __construct(
          \Magento\Backend\App\Action\Context $context,
          \Magento\Framework\View\Result\PageFactory $resultPageFactory,
          \Ced\CreditLimit\Helper\Data $helper,
          \Ced\CreditLimit\Model\CreditLimit $creditLimit
        )
        {
            parent::__construct($context);
            $this->resultPageFactory = $resultPageFactory;
            $this->helper = $helper;
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
    		try {
    			
	    		$creditLimit = $this->creditLimit->load($this->getRequest()->getParam('id'));

	    		$totalLimit = $creditLimit->getCreditAmount();
	    		$remainingAmount = $creditLimit->getRemainingAmount();
	    		$finalAmount = $remainingAmount+$data['amount_paid'];
	    		if($data['amount_paid']>$creditLimit->getPaymentDue()){
	    			$this->messageManager->addErrorMessage(__('Paying more than due balance'));
	    			return $resultRedirect->setPath('*/*/pay', ['id' => $this->getRequest()->getParam('id')]);
	    		}
	    		
	    		$creditLimit->setRemainingAmount($finalAmount);
	    		if($creditLimit->getPaymentDue()){
	                $totalDue = $creditLimit->getPaymentDue() - $data['amount_paid'];
	                if($totalDue>=0){
	                	$creditLimit->setPaymentDue($totalDue);
	                }else{
	                	$creditLimit->setPaymentDue(0.00);
	               }
	           }else{
	                $creditLimit->setPaymentDue(0.00);
	           }
	    		$creditLimit->save();
	    		$model = $this->_objectManager->create('Ced\CreditLimit\Model\Transaction');
	    		$model->setCustomerId($this->getRequest()->getParam('customer_id'));
	    		$model->setAmountPaid($data['amount_paid']);
	    		$model->setTransactionId($data['transaction_id']);
	    		$model->setCreatedAt($data['created_at']);
	    	
	    		$model->save();
	    		
	    		$this->messageManager->addSuccess(__('Balance Added Successfully'));
	    		$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
	    		if ($this->getRequest()->getParam('back')) {
	    			return $resultRedirect->setPath('*/*/pay', ['id' => $this->getRequest()->getParam('id'), '_current' => true]);
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
    	return $resultRedirect->setPath('*/*/pay', ['id' => $this->getRequest()->getParam('id')]);
    	}
    	else{
    		$this->messageManager->addError(__('No Data To Save'));
    	}
    	return $resultRedirect->setPath('*/*/');
    }
}
