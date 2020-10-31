<?php

namespace Bss\CustomizeCreditLimit\Block\Search;

/**
 * Sales order search result block
 */
class Result extends \Bss\CustomizeCreditLimit\Block\CreditLimit\Index
{
    /**
     * @var string
     */
    protected $_template = 'creditlimit/result.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
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
     * @return \DCKAP\Ordersearch\Model\Ordersearch
     */
    public function getOrders()
    {
        $obj = $this->coreRegistry->registry('creditsearchresult');
        $customerId = $this->_customerSession->getCustomerId();
        $orderCollection = $this->creditOrderFactory->create();

        $orderCollection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $orderCollection->addFieldToFilter('check_paid_order', ['null' => true]);

        $order_ids =  $orderCollection->getColumnValues('order_id');

        $salesOrderCollection = $obj->addFieldToFilter('entity_id',['in'=>$order_ids])
            ->addFieldToFilter('state', ['nin' => ['canceled','closed']]);
        $salesOrderCollection->setOrder('entity_id','DESC');
        //get values of current page
        $page = $this->getRequest()->getParam('p', 1);
        //get values of current limit
        $pageSize = $this->getRequest()->getParam('limit', 10);
        $salesOrderCollection->setPageSize($pageSize);
        $salesOrderCollection->setCurPage($page);
        return $salesOrderCollection;
    }
}
