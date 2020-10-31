<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model\ResourceModel;

class Products extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    const ORDER_ITEM = 'sales_order_item';


    const MAIN_TABLE = 'main_table';

    /**
     * @var \DCKAP\Ordersearch\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;

    /** @var \Magento\Sales\Model\ResourceModel\Order\ProductCollection */
    protected $products;

    /**
     * @var \DCKAP\Ordersearch\Model\Products
     */
    protected $orderProduct;

    /**
     * Products constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \DCKAP\Ordersearch\Model\Products $orderProduct
     * @param \DCKAP\Ordersearch\Helper\Data $helper
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \DCKAP\Ordersearch\Model\Products $orderProduct,
        \DCKAP\Ordersearch\Helper\Data $helper,
        $connectionName = null
    )
    {
        $this->orderProduct = $orderProduct;
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
    public function getProducts($dataObject)
    {

        if (!$this->products) {

            $dataObject['filter_value'] = htmlentities($dataObject['filter_value']);

            $this->products = $this->orderProduct->getCollection()->addFieldToSelect(
                'customer_id'
            );
            $this->products->getSelect()
                ->joinLeft(self::ORDER_ITEM, self::MAIN_TABLE . '.entity_id =' . self::ORDER_ITEM . '.order_id', [self::ORDER_ITEM . '.sku', self::ORDER_ITEM . '.name', self::ORDER_ITEM . '.product_id']);
            $this->products->addFieldToFilter(self::ORDER_ITEM . "." .
                $dataObject['filter_id'], ['like' => "%" . $dataObject['filter_value'] . "%"]);

            $this->products->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            );
        }

        $this->products->getSelect()->group(self::ORDER_ITEM . '.name');
        $this->products->getSelect()->limit($this->helper->getProductResultsCount());

        return $this->products;
    }

}
