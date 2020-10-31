<?php

namespace Bss\CustomizeCreditLimit\Block\Search;

/**
 * Sales order search result block
 */
class ResultPaid extends \Bss\CustomizeCreditLimit\Block\CreditLimit\Index
{
    /**
     * @var string
     */
    protected $_template = 'creditlimit/result_paid.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * ResultPaid constructor.
     * @param \Magento\Framework\View\Element\Template\Context $creditlimitcontext
     * @param \Magento\Customer\Model\Session $creditlimitcustomerSession
     * @param \Ced\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
     * @param \Ced\CreditLimit\Model\ResourceModel\CreditOrder\CollectionFactory $creditOrderFactory
     * @param \Ced\CreditLimit\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList\CollectionFactory $creditListCollectionFact
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\Order $order
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
        \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList\CollectionFactory $creditListCollectionFact,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct(
            $creditlimitcontext,
            $creditlimitcustomerSession,
            $transactionFactory,
            $creditOrderFactory,
            $helper,
            $orderCollectionFactory,
            $customerFactory,
            $creditListCollectionFact,
            $order,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Invoice and Payment'));
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->order->formatPrice($price);
    }

    /**
     * @return mixed
     */
    public function getPayOrders()
    {
        $obj = $this->coreRegistry->registry('creditsearchresultpaid');
        $customerId = $this->_customerSession->getCustomerId();
        $orderCollection = $this->creditListCollectionFact->create();

        $obj->addFieldToFilter('customer_id', ['eq' => $customerId]);

        $order_ids =  $obj->getColumnValues('increment_id');
        $salesOrderCollection = $orderCollection->addFieldToFilter('order_id',['in'=>$order_ids]);
        $salesOrderCollection->setOrder('order_id','DESC');
        return $salesOrderCollection;
    }
}
