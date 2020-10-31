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

namespace Bss\StoreCredit\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreFactory;
use Bss\StoreCredit\Model\ResourceModel\Credit as ResourceModelCredit;
use Bss\StoreCredit\Api\Data\StoreCreditInterface;

/**
 * Class Credit
 * @package Bss\StoreCredit\Model
 */
class Credit extends AbstractModel implements StoreCreditInterface
{
    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    private $storeFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreFactory $storeFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreFactory $storeFactory
    ) {

        parent::__construct(
            $context,
            $registry
        );
        $this->storeFactory = $storeFactory;
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModelCredit::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalanceAmount($amount)
    {
        return $this->setData(self::BALANCE_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalanceAmount()
    {
        return $this->getData(self::BALANCE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getCreditInvoice($order)
    {
        $invoiceBaseBssStorecreditAmount = 0;
        if ($order->hasInvoices()) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoiceBaseBssStorecreditAmount += $invoice->getBaseBssStorecreditAmount();
            }
        }
        return $invoiceBaseBssStorecreditAmount;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getCreditCreditmemo($order)
    {
        $creditmemoBaseBssStorecreditAmount = 0;
        if ($order->hasCreditmemos()) {
            foreach ($order->getCreditmemosCollection() as $creditmemo) {
                $creditmemoBaseBssStorecreditAmount += $creditmemo->getBaseBssStorecreditAmount();
            }
        }
        return $creditmemoBaseBssStorecreditAmount;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getCreditRefundBase($order)
    {
        $creditmemoBaseBssStorecreditAmountRefund = 0;
        if ($order->hasCreditmemos()) {
            foreach ($order->getCreditmemosCollection() as $creditmemo) {
                $creditmemoBaseBssStorecreditAmountRefund += $creditmemo->getBaseBssStorecreditAmountRefund();
            }
        }
        return $creditmemoBaseBssStorecreditAmountRefund;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getCreditRefund($order)
    {
        $creditmemoBssStorecreditAmountRefund = 0;
        if ($order->hasCreditmemos()) {
            foreach ($order->getCreditmemosCollection() as $creditmemo) {
                $creditmemoBssStorecreditAmountRefund += $creditmemo->getBssStorecreditAmountRefund();
            }
        }
        return $creditmemoBssStorecreditAmountRefund;
    }

    /**
     * Load store credit by customer
     *
     * @param   int $customerId
     * @return  $this
     */
    public function loadByCustomer($customerId)
    {
        return $this->_getResource()->loadByCustomer($this, $customerId);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param float|null $baseBalance
     * @return bool
     */
    public function validateBalance($quote, $baseBalance = null)
    {
        $store = $this->storeFactory->create()->load($quote->getStoreId());
        $this->setWebsiteId($store->getWebsiteId());
        $credit = $this->loadByCustomer($quote->getCustomerId());
        if (!$credit->getId() || ($baseBalance == 0 && !$quote->getBaseBssStorecreditAmount())) {
            return false;
        }
        if ($credit->getBalanceAmount() < $baseBalance) {
            return false;
        }
        return true;
    }
}
