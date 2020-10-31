<?php

namespace Bss\ShippingFee\Block\Adminhtml\Order\Invoice\Create;

class Items extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items
{
	public function getUpdateUrl()
    {
        return $this->getUrl('sales/order_invoice/updateQty', ['order_id' => $this->getInvoice()->getOrderId()]);
    }
}
