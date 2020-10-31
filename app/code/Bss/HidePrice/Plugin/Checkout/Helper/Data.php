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
 * @package    Bss_HidePrice
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\HidePrice\Plugin\Checkout\Helper;

/**
 * Class Data
 *
 * @package Bss\HidePrice\Plugin\Checkout\Helper
 */
class Data
{
    /**
     * @var \Bss\HidePrice\Helper\CartHidePrice
     */
    protected $cartHidePrice;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * Data constructor.
     * @param \Bss\HidePrice\Helper\CartHidePrice $cartHidePrice
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
        \Bss\HidePrice\Helper\CartHidePrice $cartHidePrice,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->cartHidePrice = $cartHidePrice;
        $this->cart = $cart;
    }

    /**
     * @param \Magento\Checkout\Helper\Data $subject
     * @param $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanOnepageCheckout(
        \Magento\Checkout\Helper\Data $subject,
        $result
    ) {
        $quote = $this->cart->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item->getProductType() == 'configurable') {
                $parentProductId = $item->getProductId();
                $childProductSku = $item->getSku();
                $canShowPrice = $this->cartHidePrice->canShowPrice($parentProductId, $childProductSku, true);
            } else {
                $canShowPrice = $this->cartHidePrice->canShowPrice($item->getProductId(), false, true);
            }
            if (!$canShowPrice) {
                return false;
            }
        }
        return $result;
    }
}
