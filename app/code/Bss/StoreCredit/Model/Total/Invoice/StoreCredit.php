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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Bss\StoreCredit\Helper\Data;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class StoreCredit
 * @package Bss\StoreCredit\Model\Total\Invoice
 */
class StoreCredit extends AbstractTotal
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @param Data $bssStoreCreditHelper
     * @param array $data
     */
    public function __construct(
        Data $bssStoreCreditHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
    }

    /**
     * @param Invoice $invoice
     * @return $this|void
     */
    public function collect(
        Invoice $invoice
    ) {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        $baseBalance = $order->getBaseBssStorecreditAmount();
        $balance = $order->getBssStorecreditAmount();
        if (!$order->getId() || !$baseBalance) {
            return;
        }
        if (!$invoice->getId() && !empty($order->getInvoiceCollection()->getData())) {
            $invoiceBaseBssStorecreditAmount = 0;
            $invoiceBssStorecreditAmount = 0;
            foreach ($order->getInvoiceCollection() as $invoiceOrder) {
                $invoiceBaseBssStorecreditAmount += $invoiceOrder->getBaseBssStorecreditAmount();
                $invoiceBssStorecreditAmount += $invoiceOrder->getBssStorecreditAmount();
            }
            $baseBalance -= $invoiceBssStorecreditAmount;
            $balance -= $invoiceBaseBssStorecreditAmount;
        }
        $baseGrandTotal = $invoice->getBaseGrandTotal();
        $grandTotal = $invoice->getGrandTotal();
        if ($baseBalance >= $baseGrandTotal) {
            $baseBalanceUsedLeft = $baseGrandTotal;
            $balanceUsedLeft = $grandTotal;
            $invoice->setBaseGrandTotal(0);
            $invoice->setGrandTotal(0);
        } else {
            $baseBalanceUsedLeft = $baseBalance;
            $balanceUsedLeft = $balance;
            $invoice->setBaseGrandTotal($baseGrandTotal - $baseBalanceUsedLeft);
            $invoice->setGrandTotal($grandTotal - $balanceUsedLeft);
        }
        $invoice->setBssStorecreditAmount($balanceUsedLeft);
        $invoice->setBaseBssStorecreditAmount($baseBalanceUsedLeft);
    }
}
