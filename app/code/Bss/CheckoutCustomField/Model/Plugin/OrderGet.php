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

namespace Bss\CheckoutCustomField\Model\Plugin;

use Bss\CheckoutCustomField\Model\Observer\Sales\CustomFieldHelper;

/**
 * Class OrderGet
 *
 * @package Bss\CheckoutCustomField\Model\Plugin
 */
class OrderGet
{
    /**
     * @var CustomFieldHelper
     */
    private $customFieldHelper;

    /**
     * Init plugin
     *
     * @param CustomFieldHelper $customFieldHelper
     */
    public function __construct(
        CustomFieldHelper $customFieldHelper
    ) {
        $this->customFieldHelper = $customFieldHelper;
    }

    /**
     * Set custom field to order detail
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
    ) {
        /** @var  $order */
        foreach ($resultOrder->getItems() as $order) {
            $this->customFieldHelper->executeSetExtensionAttributes($order);
        }
        return $resultOrder;
    }
}
