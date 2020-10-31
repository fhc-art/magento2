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

namespace Bss\ShippingFee\Model\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magento\Sales\Model\Order\Invoice as SalesInvoice;
use Bss\ShippingFee\Helper\Data;

/**
 * Class Invoice
 *
 * @package Bss\ShippingFee\Model\Total
 */
class Invoice extends AbstractTotal
{
    /**
     * @var \Bss\ShippingFee\Helper\Data
     */
    private $helper;

    /**
     * Invoice constructor.
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Data $helper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->helper = $helper;
        
    }

    /**
     * Set shipping fee to base grand total and grand total
     * This function run when create new invoice and submit invoice
     *
     * @param SalesInvoice $invoice
     * @return $this|void
     */
    public function collect(
        SalesInvoice $invoice
    ) {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        $enableShippingFee = $this->helper->isEnabled();
        if (!$order->getId() || !$enableShippingFee) {
            return;
        }
        $shippingFee = $order->getBssShippingFee();
        $baseShippingFee = $order->getBaseBssShippingFee();
        $baseGrandTotal = $invoice->getBaseGrandTotal();
        $grandTotal = $invoice->getGrandTotal();

        $taxShippingFee = $shippingFee - $baseShippingFee;
        $invoice->setTaxAmount($invoice->getTaxAmount() + $taxShippingFee);
        $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $taxShippingFee);

        $invoice->setBaseGrandTotal($baseGrandTotal + $shippingFee);
        $invoice->setGrandTotal($grandTotal + $shippingFee);
        $invoice->setBssShippingFee($shippingFee);
        $invoice->setBaseBssShippingFee($baseShippingFee);
    }
}
