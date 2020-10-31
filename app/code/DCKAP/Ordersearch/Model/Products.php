<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model;

/**
 * Sales order product collection model
 */
class Products extends \Magento\Framework\Model\AbstractModel
{

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
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \DCKAP\Ordersearch\Helper\Data $helper
     */

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \DCKAP\Ordersearch\Helper\Data $helper
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
    }

    /**
     * @return int                                        .,
     */
    public function getStoreId()
    {
        return $this->helper->getCurrentStoreId();
    }

    /**
     * @return productCollection
     */

    public function getCollection()
    {

        if (!($customerId = $this->customerSession->create()->getCustomerId())) {
            return false;
        }
        return $this->orderCollectionFactory->create()->addAttributeToFilter('customer_id', ['eq' => $customerId]);
    }

}