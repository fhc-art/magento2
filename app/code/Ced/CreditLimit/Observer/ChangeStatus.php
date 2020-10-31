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
namespace Ced\CreditLimit\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class ChangeStatus
 * @package Ced\CreditLimit\Observer
 */
Class ChangeStatus implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

   /**
    * 
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
    * @param \Magento\Framework\DB\Transaction $transaction
    * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
    * @param \Magento\Sales\Model\OrderRepository $orderRepository
    * @param \Ced\CreditLimit\Model\Transaction $creditLimitTransaction
    * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
    */
    
    public function __construct(
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Magento\Sales\Model\Service\InvoiceService $invoiceService,
    	\Magento\Framework\DB\Transaction $transaction,
    	\Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
    	\Magento\Sales\Model\OrderRepository $orderRepository,
    	\Ced\CreditLimit\Model\Transaction $creditLimitTransaction,
    	\Ced\CreditLimit\Model\CreditLimit $creditLimit
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->creditLimit = $creditLimit;
        $this->creditLimitTransaction = $creditLimitTransaction;
        $this->order = $orderRepository;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
          $orderid = $observer->getEvent()->getOrderIds();
          $status = $this->_scopeConfig->getValue('payment/paybycredit/order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
          foreach($orderid as $_orderId){
          	$flag = false;
          	$order =$this->order->get($_orderId);
          	if(!$order->getCustomerIsGuest()){
          	foreach($order->getAllItems() as $items){
          		
          		if($items->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU){
          			$this->performTransaction($order,$items);
          			$flag =true;
          			break;
          		}
          	}
          	if($flag){
          		$order->setCreditdueOrder(true);
          		$order->save();
          	}
          	if($order->getPayment()->getMethodInstance()->getCode()=="paybycredit"){
          		
          			if($status!='processing'){
          					$order->setState('pending_payment')->setStatus($status);
          					$order->save();
          				}else{
          					if($order->canInvoice()) {
          						$invoice = $this->_invoiceService->prepareInvoice($order);
          						$invoice->register();
          						$invoice->save();
          						$transactionSave = $this->_transaction->addObject(
          								$invoice
          						)->addObject(
          								$invoice->getOrder()
          						);
          						$transactionSave->save();
          						$this->invoiceSender->send($invoice);
          						$order->addStatusHistoryComment(
          								__('Notified customer about invoice #%1.', $invoice->getId())
          						)
          						->setIsCustomerNotified(true)
          						->save();
          						$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
          						$order->save();
          					}
          				}
          			}
          		}
          		
          	}
          return $this;
    }
    
    /**
     * 
     * @param  $order
     * @param  $item
     */
    protected function performTransaction($order,$item){
    	
    	$creditLimit = $this->creditLimit->load($order->getCustomerId(),'customer_id');
    	$totalLimit = $creditLimit->getCreditAmount();
    	$remainingAmount = $creditLimit->getRemainingAmount();
    	$finalAmount = $remainingAmount+$item->getBaseOriginalPrice();
    	
    	$paymentDue = $creditLimit->getPaymentDue()-$item->getBaseOriginalPrice();
    	if($paymentDue>=0){
    		$creditLimit->setPaymentDue($paymentDue);
    	}else{
    		$creditLimit->setPaymentDue(0.00);
    	}
    	
    	$creditLimit->setRemainingAmount($finalAmount);
    	$creditLimit->save();
    	$model = $this->creditLimitTransaction;
    	$model->setCustomerId($order->getCustomerId());
    	$model->setAmountPaid($item->getBaseOriginalPrice());
    	$model->setTransactionId($order->getIncrementId());
    	$model->setCreatedAt($order->getCreatedAt()); 
    	$model->save();
    }
}    

