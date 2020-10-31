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
namespace Ced\CreditLimit\Block\CreditLimit;
use Magento\Framework\Exception\NoSuchEntityException;
/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends \Magento\Framework\View\Element\Template
{
	
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $_customerSession;

    protected $collection = null;
    
    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $creditlimitcontext
     * @param \Magento\Customer\Model\Session $creditlimitcustomerSession
     * @param \Ced\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
     * @param \Ced\CreditLimit\Model\ResourceModel\CreditOrder\CollectionFactory $creditOrderFactory
     * @param \Ced\CreditLimit\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $creditlimitcontext,
        \Magento\Customer\Model\Session $creditlimitcustomerSession,
    	\Ced\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory,
    	\Ced\CreditLimit\Model\ResourceModel\CreditOrder\CollectionFactory $creditOrderFactory,
    	\Ced\CreditLimit\Helper\Data $helper,
    	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
    	\Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->_customerSession = $creditlimitcustomerSession;
        $this->helper = $helper;
        $this->customer = $customerFactory;
        $this->transactionFactory = $transactionFactory;
        $this->creditOrderFactory = $creditOrderFactory;
        $this->orderCollection = $orderCollectionFactory;
        parent::__construct($creditlimitcontext,$data);
    }

    /**
     * @return mixed
     */
    
  public function getCreditLimit(){
  		
  	return $this->helper->getCustomerCreditLimit($this->_customerSession->getCustomerId());
  }

    /**
     * @return mixed
     */
  public function getPaidHistory(){
  	
  	$collection = $this->getCreditLimitCollection();
  	
  	if($this->collection!==null)
  		return $this->collection;
  		
	  $this->collection = new \Ced\CreditLimit\Helper\Pager($collection->getData(), ($this->getRequest()->getParam('page')!==null ? $this->getRequest()->getParam('page') : 1), 5);
	  $this->collection->setShowFirstAndLast(false);
	  $this->collection->setMainSeperator('  ');

  	  return $this->collection;
  }
  
/**
 * @return object
 */
  public function getCreditLimitCollection(){
  	
  	return $this->transactionFactory->create()->addFieldToFilter('customer_id',$this->_customerSession->getCustomerId());
  
  }
  
    /**
     * @param $limit_amount
     * @return mixed
     */
  public function addCurrencyInAmount($limit_amount){
    
    if(!$limit_amount)
        $limit_amount = 0;
    return $this->helper->getFormattedPrice($limit_amount);
  }

    /**
     * @return mixed
     */
  public function getOrders(){
    
    $customer_id = $this->_customerSession->getCustomerId();
    $orderCollection = $this->creditOrderFactory->create();

    $orderCollection->addFieldToFilter('customer_id', ['eq' => $customer_id]);

    $order_ids =  $orderCollection->getColumnValues('order_id');

    $salesOrderCollection = $this->orderCollection->create()->addFieldToFilter('entity_id',['in'=>$order_ids]);
    $salesOrderCollection->setOrder('entity_id','DESC');
    return $salesOrderCollection;

  }

    /**
     * @return $this|\Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        $viewUrl =  $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
        return $viewUrl;
    }

    /**
     * @param object $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        $realOrderUrl =  $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
        return  $realOrderUrl;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
    	$this->getLayout()->createBlock();
        $backUrl =  $this->getUrl('customer/account/');
        return $backUrl;
    }
    
  
}
