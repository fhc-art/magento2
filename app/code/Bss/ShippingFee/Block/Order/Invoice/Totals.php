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

namespace Bss\ShippingFee\Block\Order\Invoice;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Template\Context;
use Bss\ShippingFee\Helper\Data;

/**
 * Class Totals
 *
 * @package Bss\ShippingFee\Block\Order\Invoice
 */
class Totals extends Template
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

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
        parent::__construct(
            $context,
            $data
        );
        $this->dataObjectFactory = $dataObjectFactory;
        $this->helper = $helper;
    }

    /**
     * Add shipping fee invoice totals array
     *
     * @return $this
     */
    public function initTotals()
    {
        $invoiceTotalsBlock = $this->getParentBlock();
        $invoice = $invoiceTotalsBlock->getInvoice();
        $title = $this->helper->getTitle();
        if ($invoice->getBssShippingFee() > 0) {
            $total = $this->dataObjectFactory->create();
            $total->setCode('bss_shipping_fee')
                ->setValue($invoice->getBaseBssShippingFee())
                ->setBaseValue($invoice->getBaseBssShippingFee())
                ->setLabel(__($title));
            $invoiceTotalsBlock->addTotal($total);
        }
    }
}
