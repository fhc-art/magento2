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

namespace Bss\StoreCredit\Plugin\Sales;

use Bss\StoreCredit\Model\Credit;
use Magento\Sales\Model\Order as SalesOrder;

/**
 * Class Order
 * @package Bss\StoreCredit\Plugin\Sales
 */
class Order
{
    /**
     * @var \Bss\StoreCredit\Model\Credit
     */
    private $creditModel;

    /**
     *
     * @param Credit $creditModel
     */
    public function __construct(
        Credit $creditModel
    ) {
        $this->creditModel = $creditModel;
    }

    /**
     * @param SalesOrder $order
     * @return void
     */
    public function beforeCanCreditmemo(
        SalesOrder $order
    ) {
        $invoiceBaseBssStorecreditAmount = $this->creditModel->getCreditInvoice($order);
        $creditmemoBaseBssStorecreditAmountRefund = $this->creditModel->getCreditRefundBase($order);
        $amount = $invoiceBaseBssStorecreditAmount - $creditmemoBaseBssStorecreditAmountRefund;
        if ($order->hasCreditmemos() && $order->getBaseBssStorecreditAmount() && $amount > 0) {
            $order->setForcedCanCreditmemo(true);
        }
    }
}
