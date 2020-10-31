<?php

namespace Bss\ShippingFee\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

Class InvoiceNew implements ObserverInterface
{
	/**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

	public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$orderId = $observer->getEvent()->getRequest()->getParam('order_id');
    	$order = $this->orderRepository->get($orderId);
    	$order->setBaseBssShippingFee(0);
        $order->setBssShippingFee(0);
        $order->save();
        return $this;
    }
}
