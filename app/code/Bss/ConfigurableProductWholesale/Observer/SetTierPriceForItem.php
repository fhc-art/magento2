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
 * @category  BSS
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Bss\ConfigurableProductWholesale\Helper\Data;

/**
 * Class SetTierPriceForItem
 *
 * @package Bss\ConfigurableProductWholesale\Observer
 */
class SetTierPriceForItem implements ObserverInterface
{
    /**
     * @var \Bss\ConfigurableProductWholesale\Helper\Data
     */
    private $helperBss;

    /**
     * @param Data $helperBss
     */
    public function __construct(
        Data $helperBss
    ) {
        $this->helperBss = $helperBss;
    }

    /**
     * Advanced tier price
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helperBss->getConfig('tier_price_advanced')) {
            $item = $observer->getEvent()->getQuoteItem();
            $itemType = $item->getProduct()->getTypeId();
            if ($itemType == Data::CONFIGURABLE_PRODUCT_TYPE) {
                $this->helperBss->setPriceForItem($item);
            }
        }
    }
}
