<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ShippingFee
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ShippingFee\Block\Adminhtml\Order\Totals;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObjectFactory;
use Bss\ShippingFee\Helper\Data;

/**
 * Class ShippingFee
 *
 * @package Bss\ShippingFee\Block\Adminhtml\Order\Totals
 */
class ShippingFee extends Template
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var \Bss\ShippingFee\Helper\Data
     */
    private $helper;

    /**
     * @param Context $context
     * @param DataObjectFactory $dataObjectFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataObjectFactory $dataObjectFactory,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataObjectFactory = $dataObjectFactory;
        $this->helper = $helper;
    }

    /**
     * Get order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Add shipping fee info to total
     *
     * @return $this
     */
    public function initTotals()
    {
        $source = $this->getParentBlock()->getSource();
        $total = $this->dataObjectFactory->create();
        $title = $this->helper->getTitle();
        $total->setCode('bss_shipping_fee')
            ->setValue($source->getBaseBssShippingFee())
            ->setBaseValue($source->getBaseBssShippingFee())
            ->setLabel(__($title));
        $this->getParentBlock()->addTotalBefore($total, 'tax');
        
        return $this;
    }
}
