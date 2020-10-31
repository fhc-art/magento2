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

/**
 * Class SetPriceForCart
 *
 * @package Bss\ConfigurableProductWholesale\Observer
 */
class SetPriceForCart implements ObserverInterface
{
    /**
     * @var \Bss\ConfigurableProductWholesale\Helper\Data
     */
    private $helperBss;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Bss\ConfigurableProductWholesale\Helper\Data $helperBss
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Bss\ConfigurableProductWholesale\Helper\Data $helperBss
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helperBss = $helperBss;
    }

    /**
     * Advanced tier price
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helperBss->getConfig('tier_price_advanced')) {
            $productIds = [];
            if (!empty($this->checkoutSession)) {
                $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
                foreach ($quoteItems as $quoteItem) {
                    $productId = $quoteItem->getProduct()->getId();
                    $quoteItemType = $quoteItem->getProduct()->getTypeId();
                    if ($quoteItemType != 'configurable' || in_array($productId, $productIds)) {
                        continue;
                    }
                    $this->helperBss->setPriceForItem($quoteItem);
                    array_push($productIds, $productId);
                }
            }
        }
    }
}
