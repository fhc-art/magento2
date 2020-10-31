<?php

namespace Bss\ShippingFee\Model\Order;

class Invoice extends \Magento\Sales\Model\Order\Invoice
{
	/**#@+
     * Invoice states
     */
    const STATE_OPEN = 1;
    /**#@-*/

    const CAPTURE_ONLINE = 'online';

    const CAPTURE_OFFLINE = 'offline';

	public function register()
    {
        if ($this->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot register an existing invoice'));
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();
            } else {
                $item->isDeleted(true);
            }
        }

        $order = $this->getOrder();
        $captureCase = $this->getRequestedCaptureCase();
        if ($this->canCapture()) {
            if ($captureCase) {
                if ($captureCase == self::CAPTURE_ONLINE) {
                    $this->capture();
                } elseif ($captureCase == self::CAPTURE_OFFLINE) {
                    $this->setCanVoidFlag(false);
                    $this->pay();
                }
            }
        } elseif (!$order->getPayment()->getMethodInstance()->isGateway() || $captureCase == self::CAPTURE_OFFLINE) {
            if (!$order->getPayment()->getIsTransactionPending()) {
                $this->setCanVoidFlag(false);
                $this->pay();
            }
        }

        $order->setTotalInvoiced($order->getTotalInvoiced() + $this->getGrandTotal());
        $order->setBaseTotalInvoiced($order->getBaseTotalInvoiced() + $this->getBaseGrandTotal());

        $order->setSubtotalInvoiced($order->getSubtotalInvoiced() + $this->getSubtotal());
        $order->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced() + $this->getBaseSubtotal());



        $shippingFee = $order->getBssShippingFee();
        $baseShippingFee = $order->getBaseBssShippingFee();
        $taxShippingFee = $shippingFee - $baseShippingFee;

        $order->setTaxInvoiced($order->getTaxInvoiced() + $this->getTaxAmount() - $taxShippingFee);
        $order->setBaseTaxInvoiced($order->getBaseTaxInvoiced() + $this->getBaseTaxAmount() - $taxShippingFee);

        $order->setDiscountTaxCompensationInvoiced(
            $order->getDiscountTaxCompensationInvoiced() + $this->getDiscountTaxCompensationAmount()
        );
        $order->setBaseDiscountTaxCompensationInvoiced(
            $order->getBaseDiscountTaxCompensationInvoiced() + $this->getBaseDiscountTaxCompensationAmount()
        );

        $order->setShippingTaxInvoiced($order->getShippingTaxInvoiced() + $this->getShippingTaxAmount());
        $order->setBaseShippingTaxInvoiced($order->getBaseShippingTaxInvoiced() + $this->getBaseShippingTaxAmount());

        $order->setShippingInvoiced($order->getShippingInvoiced() + $this->getShippingAmount());
        $order->setBaseShippingInvoiced($order->getBaseShippingInvoiced() + $this->getBaseShippingAmount());

        $order->setDiscountInvoiced($order->getDiscountInvoiced() + $this->getDiscountAmount());
        $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() + $this->getBaseDiscountAmount());
        $order->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost() + $this->getBaseCost());

        $state = $this->getState();
        if (null === $state) {
            $this->setState(self::STATE_OPEN);
        }

        $this->_eventManager->dispatch(
            'sales_order_invoice_register',
            [$this->_eventObject => $this, 'order' => $order]
        );
        return $this;
    }
}
