<?php
namespace Bss\StoreCredit\Model\Order;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\EntityInterface;

class Invoice extends \Magento\Sales\Model\Order\Invoice
{

    public function register()
    {
        if (!$this->getId() && !$this->getBssStorecreditAmount()) {
            $order = $this->getOrder();
            $quote = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Quote\Model\Quote')->loadByIdWithoutStore($order->getQuoteId());
            $baseBalance = $quote->getBaseBssStorecreditAmount();
            $balance = $quote->getBssStorecreditAmount();
            if ($baseBalance && $balance && $baseBalance > 0 && $balance > 0) {
                if ($order->getInvoiceCollection()->getSize() > 0) {
                    $invoiceBaseBssStorecreditAmount = 0;
                    $invoiceBssStorecreditAmount = 0;
                    foreach ($order->getInvoiceCollection() as $invoiceOrder) {
                        $invoiceBaseBssStorecreditAmount += $invoiceOrder->getBaseBssStorecreditAmount();
                        $invoiceBssStorecreditAmount += $invoiceOrder->getBssStorecreditAmount();
                    }
                    $baseBalance -= $invoiceBssStorecreditAmount;
                    $balance -= $invoiceBaseBssStorecreditAmount;
                }
                $baseGrandTotal = $this->getBaseGrandTotal();
                $grandTotal = $this->getGrandTotal();
                if ($baseBalance >= $baseGrandTotal) {
                    $baseBalanceUsedLeft = $baseGrandTotal;
                    $balanceUsedLeft = $grandTotal;
                    $this->setBaseGrandTotal(0);
                    $this->setGrandTotal(0);
                } else {
                    $baseBalanceUsedLeft = $baseBalance;
                    $balanceUsedLeft = $balance;
                    $this->setBaseGrandTotal($baseGrandTotal - $baseBalanceUsedLeft);
                    $this->setGrandTotal($grandTotal - $balanceUsedLeft);
                }
                $this->setBssStorecreditAmount($balanceUsedLeft);
                $this->setBaseBssStorecreditAmount($baseBalanceUsedLeft);
            }
        }
        return parent::register();
    }

}
