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
 * @category   BSS
 * @package    Bss_CheckoutCustomField
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CheckoutCustomField\Model\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderLoadAfter
 *
 * @package Bss\CheckoutCustomField\Model\Observer\Sales
 */
class OrderLoadAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtension
     */
    protected $orderExtension;

    /**
     * @var CustomFieldHelper
     */
    private $customFieldHelper;

    /**
     * OrderLoadAfter constructor.
     *
     * @param CustomFieldHelper $customFieldHelper
     * @param \Magento\Sales\Api\Data\OrderExtension $orderExtension
     */
    public function __construct(
        CustomFieldHelper $customFieldHelper,
        \Magento\Sales\Api\Data\OrderExtension $orderExtension
    ) {
        $this->orderExtension = $orderExtension;
        $this->customFieldHelper = $customFieldHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->customFieldHelper->setExtensionObject($this->orderExtension);
        $this->customFieldHelper->executeSetExtensionAttributes($observer->getOrder());
    }
}
