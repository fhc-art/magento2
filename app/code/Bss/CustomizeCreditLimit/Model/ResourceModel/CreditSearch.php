<?php

namespace Bss\CustomizeCreditLimit\Model\ResourceModel;

class CreditSearch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const ORDER_ADDRES = 'sales_order_address';

    const ORDER_ITEM = 'sales_order_item';

    const MAIN_TABLE = 'main_table';

    const INVOICE_TABLE = 'sales_invoice';

    /**
     * @var \DCKAP\Ordersearch\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;

    protected $ordersPaid;

    /**
     * @var CreditList\CollectionFactory
     */
    protected $creditListCollectionFact;

    /**
     * Ordersearch constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \DCKAP\Ordersearch\Helper\Data $helper
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \DCKAP\Ordersearch\Helper\Data $helper,
        \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList\CollectionFactory $creditListCollectionFact,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $connectionName = null
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->orderConfig = $orderConfig;
        $this->helper = $helper;
        $this->creditListCollectionFact = $creditListCollectionFact;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {

    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders($dataObject)
    {
        if (!($customerId = $this->customerSession->create()->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addAttributeToFilter('customer_id', ['eq' => $customerId]);

            if (isset($dataObject['filter_id']) && $dataObject['filter_id'] == 'created_at') {
                if (!empty($dataObject['order_from_date']) || !empty($dataObject['order_to_date'])) {
                    $this->orderDateFilter($dataObject);
                }
            }

            if ((isset($dataObject['filter_id']) && !empty($dataObject['filter_id'])) && isset($dataObject['filter_value']) && !empty($dataObject['filter_value'])) {
                if ($dataObject['filter_id'] == 'sku') {
                    $this->orderProductFilter($dataObject);
                } elseif ($dataObject['filter_id'] == 'invoice') {
                    $this->invoiceFilter($dataObject);
                } elseif ($dataObject['filter_id'] == 'purchase_order') {
                    $this->orderPOFilter($dataObject);
                } else {
                    $this->orders->addAttributeToFilter($dataObject['filter_id'], ['like' => "%" . $dataObject['filter_value'] . "%"]
                    );
                }
            }

            $this->orders->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            );
            $this->orders->getSelect()->group(self::MAIN_TABLE . '.entity_id');
        }
        return $this->orders;
    }

    /**
     * @param array $dataObject
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function orderPOFilter($dataObject)
    {
        $filterValue = $dataObject['filter_value'];
        $poOrdersId=array();
        $ordersData = clone $this->orders;
        foreach ($ordersData as $order)
        {
            if($order->getData('bss_customfield')) {
                $bssCustomField = json_decode($order->getData('bss_customfield'),true);
                if(!empty($bssCustomField) && array_key_exists('purchase_order' , $bssCustomField)) {
                    $poValue = $bssCustomField['purchase_order']['value'];
                    if ($poValue == $filterValue || $hasValue = stripos($poValue, $filterValue) !== false)
                    {
                        array_push($poOrdersId,$order['entity_id']);
                    }
                }
            }
        }
        $this->orders->addFieldToFilter('entity_id',  array('in' => $poOrdersId));
    }

    /**
     * @var array $dataObject
     */
    protected function orderDateFilter($dataObject)
    {
        $timezone = $this->scopeConfig->getvalue('general/locale/timezone');
        if (!empty($dataObject['order_from_date']) && !empty($dataObject['order_to_date'])) {
            $fromDate = $this->helper->getFromDate($dataObject['order_from_date']);
            $fromDate = new \DateTime($fromDate, new \DateTimeZone($timezone));
            $fromDate = $fromDate->format('U');
            $fromDate = date("Y-m-d H:i:s",$fromDate);
            $toDate = $this->helper->getToDate($dataObject['order_to_date']);
            $toDate = new \DateTime($toDate, new \DateTimeZone($timezone));
            $toDate = $toDate->format('U');
            $toDate = date("Y-m-d H:i:s",$toDate);

            $this->orders->addAttributeToFilter(self::MAIN_TABLE . '.created_at', ['from' => $fromDate, 'to' => $toDate]);
        } elseif (!empty($dataObject['order_from_date'])) {
            $fromDate = $this->helper->getFromDate($dataObject['order_from_date']);
            $fromDate = new \DateTime($fromDate, new \DateTimeZone($timezone));
            $fromDate = $fromDate->format('U');
            $fromDate = date("Y-m-d H:i:s",$fromDate);
            $this->orders->addAttributeToFilter(self::MAIN_TABLE . '.created_at', ['from' => $fromDate]);
        } else {
            $toDate = $this->helper->getToDate($dataObject['order_to_date']);
            $toDate = new \DateTime($toDate, new \DateTimeZone($timezone));
            $toDate = $toDate->format('U');
            $toDate = date("Y-m-d H:i:s",$toDate);
            $this->orders->addAttributeToFilter(self::MAIN_TABLE . '.created_at', ['to' => $toDate]);
        }
    }

    /**
     * @var array $dataObject
     */
    protected function orderProductFilter($dataObject)
    {
        $this->orders->getSelect()->joinLeft(
            self::ORDER_ITEM,
            self::MAIN_TABLE . '.entity_id =' . self::ORDER_ITEM . '.order_id',
            [self::ORDER_ITEM . '.sku']
        );
        $this->orders
            ->addAttributeToFilter(
                self::ORDER_ITEM . "." . $dataObject['filter_id'], ['like' => "%" . $dataObject['filter_value'] . "%"]
            );
    }

    /**
     * @param array $dataObject
     */
    protected function invoiceFilter($dataObject)
    {
        $this->orders->getSelect()->joinLeft(
            self::INVOICE_TABLE,
            self::MAIN_TABLE . '.entity_id =' . self::INVOICE_TABLE . '.order_id',
            ['invoice_increment_id' => self::INVOICE_TABLE . '.increment_id']
        );
        $this->orders
            ->addAttributeToFilter(
                self::INVOICE_TABLE . ".increment_id" , ['like' => "%" . $dataObject['filter_value'] . "%"]
            );
    }

    public function getOrdersPaid($dataObject)
    {
        if (!($customerId = $this->customerSession->create()->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addAttributeToFilter('customer_id', ['eq' => $customerId]);

            if (isset($dataObject['filter_id']) && $dataObject['filter_id'] == 'created_at') {
                if (!empty($dataObject['order_from_date']) || !empty($dataObject['order_to_date'])) {
                    $this->orders->getSelect()->joinLeft(
                        'customize_ced_credit_list',
                        self::MAIN_TABLE . '.increment_id =' . 'customize_ced_credit_list' . '.order_id',
                        ['order_increment_id' => 'customize_ced_credit_list' . '.order_id']
                    );
                    $timezone =  $timezone = $this->scopeConfig->getvalue('general/locale/timezone');;

                    if (!empty($dataObject['order_from_date']) && !empty($dataObject['order_to_date'])) {
                        $fromDate = $this->helper->getFromDate($dataObject['order_from_date']);
                        $fromDate = new \DateTime($fromDate, new \DateTimeZone($timezone));
                        $fromDate = $fromDate->format('U');
                        $fromDate = date("Y-m-d H:i:s",$fromDate);
                        $toDate = $this->helper->getToDate($dataObject['order_to_date']);
                        $toDate = new \DateTime($toDate, new \DateTimeZone($timezone));
                        $toDate = $toDate->format('U');
                        $toDate = date("Y-m-d H:i:s",$toDate);

                        $this->orders->addAttributeToFilter('customize_ced_credit_list' . '.date_paid', ['from' => $fromDate, 'to' => $toDate]);
                    } elseif (!empty($dataObject['order_from_date'])) {
                        $fromDate = $this->helper->getFromDate($dataObject['order_from_date']);
                        $fromDate = new \DateTime($fromDate, new \DateTimeZone($timezone));
                        $fromDate = $fromDate->format('U');
                        $fromDate = date("Y-m-d H:i:s",$fromDate);
                        $this->orders->addAttributeToFilter('customize_ced_credit_list' . '.date_paid', ['from' => $fromDate]);
                    } else {
                        $toDate = $this->helper->getToDate($dataObject['order_to_date']);
                        $toDate = new \DateTime($toDate, new \DateTimeZone($timezone));
                        $toDate = $toDate->format('U');
                        $toDate = date("Y-m-d H:i:s",$toDate);
                        $this->orders->addAttributeToFilter('customize_ced_credit_list' . '.date_paid', ['to' => $toDate]);
                    }
                }
            }

            if ((isset($dataObject['filter_id']) && !empty($dataObject['filter_id'])) && isset($dataObject['filter_value']) && !empty($dataObject['filter_value'])) {
                if ($dataObject['filter_id'] == 'sku') {
                    $this->orderProductFilter($dataObject);
                } elseif ($dataObject['filter_id'] == 'invoice') {
                    $this->invoiceFilter($dataObject);
                } elseif ($dataObject['filter_id'] == 'purchase_order') {
                    $this->orderPOFilter($dataObject);
                } else {
                    $this->orders->addAttributeToFilter($dataObject['filter_id'], ['like' => "%" . $dataObject['filter_value'] . "%"]
                    );
                }
            }

            $this->orders->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            );
            $this->orders->getSelect()->group(self::MAIN_TABLE . '.entity_id');
        }
        return $this->orders;
    }
}
