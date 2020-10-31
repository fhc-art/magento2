<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\StorePickup\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddHtmlToOrderShippingViewObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function execute(EventObserver $observer)
    {
        if($observer->getElementName() == 'order_shipping_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            if($order->getPickupStore() != '' && $order->getShippingMethod() == "storepickup_storepickup")
            {
                $localeDate = $this->objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                if($order->getPickupDate() != '0000-00-00 00:00:00') {
                    $formattedDate = $localeDate->formatDateTime(
                        $order->getPickupDate(),
                        \IntlDateFormatter::MEDIUM,
                        \IntlDateFormatter::MEDIUM,
                        null,
                        $localeDate->getConfigTimezone(
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            $order->getStore()->getCode()
                        )
                    );
                } else {
                    $formattedDate = __('N/A');
                }
                $storePickupBlock = $this->objectManager->create('Magento\Framework\View\Element\Template');
                $storePickupBlock->setPickupDate($formattedDate);
                $storePickupBlock->setPickupStore($order->getPickupStore());
                $storePickupBlock->setTemplate('Mageants_StorePickup::order_shipping_info.phtml');
                $html = $observer->getTransport()->getOutput() . $storePickupBlock->toHtml();
                $observer->getTransport()->setOutput($html);
            }
        }
    }
}
