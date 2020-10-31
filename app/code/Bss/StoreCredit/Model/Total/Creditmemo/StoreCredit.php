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

namespace Bss\StoreCredit\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Bss\StoreCredit\Helper\Data;
use Bss\StoreCredit\Model\Credit;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class StoreCredit
 * @package Bss\StoreCredit\Model\Total\Creditmemo
 */
class StoreCredit extends AbstractTotal
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Bss\StoreCredit\Model\Credit
     */
    private $creditModel;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param Data $bssStoreCreditHelper
     * @param Credit $creditModel
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Data $bssStoreCreditHelper,
        Credit $creditModel,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($data);
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->creditModel = $creditModel;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Collect storecredit of refunded items
     *
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);

        $order = $creditmemo->getOrder();
        if (!$order->getBaseBssStorecreditAmount()) {
            return $this;
        }

        $invoiceBaseBssStorecreditAmount = $this->creditModel->getCreditInvoice($order);
        $creditmemoBaseBssStorecreditAmountRefund = $this->creditModel->getCreditCreditmemo($order);
        $baseBalance = $invoiceBaseBssStorecreditAmount - $creditmemoBaseBssStorecreditAmountRefund;
        if ($baseBalance >= $creditmemo->getBaseGrandTotal()) {
            $baseBalanceUsedLeft = $creditmemo->getBaseGrandTotal();
            $balanceUsedLeft = $creditmemo->getGrandTotal();
            $creditmemo->setBaseGrandTotal(0)
                        ->setGrandTotal(0)
                        ->setAllowZeroGrandTotal(true);
        } else {
            $baseBalanceUsedLeft = $baseBalance;
            $balanceUsedLeft = $this->priceCurrency->convert($baseBalanceUsedLeft, $creditmemo->getStore());
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseBalanceUsedLeft)
                        ->setGrandTotal($creditmemo->getGrandTotal() - $balanceUsedLeft);
        }
        $creditmemo->setBaseBssStorecreditAmount($baseBalanceUsedLeft)
            ->setBssStorecreditAmount($balanceUsedLeft);
        return $this;
    }
}
