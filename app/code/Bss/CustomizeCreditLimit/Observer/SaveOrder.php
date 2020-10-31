<?php

namespace Bss\CustomizeCreditLimit\Observer;
use Magento\Framework\Event\ObserverInterface;

Class SaveOrder implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList
     */
    protected $creditList;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * SaveOrder constructor.
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList $creditList
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList $creditList,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->creditList = $creditList;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->order = $orderRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        foreach($orderIds as $orderId){
            $order =$this->order->get($orderId);
            if(!$order->getCustomerIsGuest()){
                $quoteId = $order->getQuoteId();
                /** @var \Magento\Quote\Model\Quote $quote */
                $quote = $this->quoteFactory->create()->load($quoteId);
                foreach ($quote->getAllItems() as $item) {
                    if ($item->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU) {
                        $orderIds = $item->getOrderCreditLimit();
                        $orderIds = explode(",", $orderIds);
                        $count = count($orderIds);
                        if ($count > 1) {
                            unset($orderIds[$count -1]);
                        }

                        $data = [];
                        $orderCollection = $this->orderCollectionFactory->create()->addFieldToFilter('entity_id', ['in'=>$orderIds]);
                        foreach ($orderCollection as $value) {
                            $arr = [];
                            $arr['customer_id'] = $value->getCustomerId();
                            $arr['order_id'] = $value->getIncrementId();
                            $arr['invoice'] = $this->setInvoice($value);
                            $arr['purchase'] = $this->getPoNumber($value->getBssCustomfield());
                            $arr['date_paid'] = $order->getCreatedAt();
                            $arr['order_amount'] = $value->getGrandTotal();
                            $data[] = $arr;
                        }
                        if (!empty($data)) {
                            $orderIds = implode(",", $orderIds);
                            $columns = ['check_paid_order' => 1];
                            $where = 'order_id IN ('.$orderIds.')';
                            $this->creditList->insertData('customize_ced_credit_list', $data);
                            $this->creditList->updateData('ced_credit_limit_order', $columns, $where);
                        }
                        break;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param mixed $order
     * @return string
     */
    public function setInvoice($order)
    {
        $invoiceCollection = $order->getInvoiceCollection();
        $invoiceArray = [];
        $invoices = '';
        if (count($invoiceCollection) > 0) {
            foreach ($invoiceCollection as $item) {
                $invoiceArray[] = $item->getIncrementId();
            }
            $invoices = implode(",", $invoiceArray);
        }
        return $invoices;
    }

    /**
     * @param mixed $bssField
     * @return |null
     */
    public function getPoNumber($bssField)
    {
        $bssCustomField = json_decode($bssField,true);
        if(!empty($bssCustomField) && array_key_exists('purchase_order' , $bssCustomField))
        {
            $poValue = $bssCustomField['purchase_order']['value'];
            return $poValue;
        }
        return null;
    }
}

