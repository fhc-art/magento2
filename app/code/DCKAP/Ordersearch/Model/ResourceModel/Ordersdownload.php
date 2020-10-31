<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model\ResourceModel;

class Ordersdownload extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    const ORDER_ADDRES = 'sales_order_address';

    const ORDER_ITEM = 'sales_order_item';

    const MAIN_TABLE = 'main_table';

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

    /**
     * @var \DCKAP\Ordersearch\Model\ResourceModel\Ordersdownload
     */
    private $ordersdownload;

    /**
     * Ordersdownload constructor.
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
        $connectionName = null)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->orderConfig = $orderConfig;
        $this->helper = $helper;
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

            if (!isset($dataObject['downloadorder'])):

                if ($this->helper->isDateFilterEnable()) {
                    if (!empty($dataObject['order_from_date']) || !empty($dataObject['order_to_date'])) {
                        $this->orderDateFilter($dataObject);
                    }
                }
                if ((isset($dataObject['filter_id']) && !empty($dataObject['filter_id'])) && isset($dataObject['filter_value']) && !empty($dataObject['filter_value'])):
                    if ($dataObject['filter_id'] == 'sku' || $dataObject['filter_id'] == 'name') {
                        $this->orderProductFilter($dataObject);

                    } elseif ($dataObject['filter_id'] == 'city' || $dataObject['filter_id'] == 'postcode') {
                        $this->orderAddressFilter($dataObject);
                    } else {
                        $this->orders->addAttributeToFilter($dataObject['filter_id'], ['eq' => $dataObject['filter_value']]
                        );
                    }
                endif;
            endif;
            $this->orders->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            );
            $this->orders->getSelect()->group(self::MAIN_TABLE . '.entity_id');
        }
        // echo $this->orders->getSelect()->__toString();die;
        return $this->orders;
    }

    /**
     * @var array
     */
    protected function orderDateFilter($dataObject)
    {

        if (!empty($dataObject['order_from_date']) && !empty($dataObject['order_to_date'])) {

            $this->orders->addAttributeToFilter(self::MAIN_TABLE . '.created_at', ['from' => $this->helper->getFromDate($dataObject['order_from_date']), 'to' => $this->helper->getToDate($dataObject['order_to_date'])]);

        } elseif (!empty($dataObject['order_from_date'])) {

            $this->orders->addAttributeToFilter(self::MAIN_TABLE . '.created_at', ['from' => $this->helper->getFromDate($dataObject['order_from_date'])]);

        } else {

            $this->orders->addAttributeToFilter(self::MAIN_TABLE . '.created_at', ['to' => $this->helper->getToDate($dataObject['order_to_date'])]);

        }
    }

    /**
     * @var array
     */
    protected function orderProductFilter($dataObject)
    {

        $this->orders->getSelect()->joinLeft(self::ORDER_ITEM, self::MAIN_TABLE . '.entity_id =' . self::ORDER_ITEM . '.order_id', [self::ORDER_ITEM . '.sku', self::ORDER_ITEM . '.name']);

        $dataObject['filter_value'] = htmlentities($dataObject['filter_value']);

        $this->orders->addAttributeToFilter(self::ORDER_ITEM . "." .
            $dataObject['filter_id'], ['like' => "%" . $dataObject['filter_value'] . "%"]);
    }

    /**
     * @var array
     */
    protected function orderAddressFilter($dataObject)
    {

        $this->orders->getSelect()->joinLeft(self::ORDER_ADDRES, self::MAIN_TABLE . '.entity_id =' . self::ORDER_ADDRES . '.parent_id', [self::ORDER_ADDRES . '.city', self::ORDER_ADDRES . '.postcode']);

        $dataObject['filter_value'] = htmlentities($dataObject['filter_value']);

        $this->orders->addAttributeToFilter(self::ORDER_ADDRES . "." .
            $dataObject['filter_id'], ['like' => "%" . $dataObject['filter_value'] . "%"])
            ->addAttributeToFilter('address_type', ["eq" => 'billing']);
    }

}
