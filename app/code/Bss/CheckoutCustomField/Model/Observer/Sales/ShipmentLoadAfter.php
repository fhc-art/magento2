<?php
/**
 *  BSS Commerce Co.
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category    BSS
 * @package     Bss_CheckoutCustomField
 * @author      Extension Team
 * @copyright   Copyright © 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license     http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CheckoutCustomField\Model\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ShipmentLoadAfter
 *
 * @package Bss\CheckoutCustomField\Model\Observer\Sales
 */
class ShipmentLoadAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\Data\ShipmentExtension
     */
    private $shipmentExtension;

    /**
     * @var CustomFieldHelper
     */
    private $customFieldHelper;

    /**
     * ShipmentLoadAfter constructor.
     *
     * @param CustomFieldHelper $customFieldHelper
     * @param \Magento\Sales\Api\Data\ShipmentExtension $shipmentExtension
     */
    public function __construct(
        CustomFieldHelper $customFieldHelper,
        \Magento\Sales\Api\Data\ShipmentExtension $shipmentExtension
    ) {
        $this->shipmentExtension = $shipmentExtension;
        $this->customFieldHelper = $customFieldHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $this->customFieldHelper->setExtensionObject($this->shipmentExtension);
        $this->customFieldHelper->executeSetExtensionAttributes(null, $observer->getData('shipment'));
    }
}
