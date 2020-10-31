<?php
/**
  * @author     DCKAP <extensions@dckap.com>
  * @package    DCKAP_Ordersearch
  * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace DCKAP\Ordersearch\Block\Order\Search;

/**
 * Sales order search result block
 */
class Result extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/result/result.phtml';

    /**
     *@var \Magento\Sales\Model\ResourceModel\Order\Collection 
     */
    protected $orders;

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
        \Magento\Framework\View\Element\Template\Context $context,  
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) { 
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Orders'));
    }
    /**
     * @return \DCKAP\Ordersearch\Model\Ordersearch
     */
    public function getOrders()
    {
        return $this->coreRegistry->registry('searchresult');
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {   
        parent::_prepareLayout();
        if ($this->getOrders()) {

            $pager = $this->getLayout()->createBlock(
                'DCKAP\Ordersearch\Block\Order\Html\Pager',
                'sales.order.history.pager'
            )
            ->setCollection($this->getOrders());

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
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
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
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
