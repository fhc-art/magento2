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

namespace Bss\ConfigurableProductWholesale\Plugin\Block\Cart;

use Magento\Framework\Registry;
use Bss\ConfigurableProductWholesale\Helper\Data;

/**
 * Class Sidebar
 *
 * @package Bss\ConfigurableProductWholesale\Plugin\Block\Cart
 */
class Sidebar
{
    const ACTIVE_CHECKOUT_CART_CONFIGURE = 'checkout_cart_configure';

    /**
     * @var Data $helperBss
     */
    private $helperBss;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Data $helperBss
     * @param Registry $registry
     */
    public function __construct(
        Data $helperBss,
        Registry $registry
    ) {
        $this->helperBss = $helperBss;
        $this->registry = $registry;
    }

    /**
     * Add item edit to json
     *
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param \Magento\Checkout\Block\Cart\Sidebar $result
     * @return \Magento\Checkout\Block\Cart\Sidebar
     */
    public function afterGetConfig(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        $itemId = (int)$subject->getRequest()->getParam('id');
        $productId = (int)$subject->getRequest()->getParam('product_id');
        $product = $this->registry->registry('product');
        $fullActionName = $subject->getRequest()->getFullActionName();
        if (!$itemId || !$product || $fullActionName != self::ACTIVE_CHECKOUT_CART_CONFIGURE) {
            return $result;
        }
        if ($this->helperBss->getConfig() && $product->getTypeId() == Data::CONFIGURABLE_PRODUCT_TYPE) {
            $result['bssConfigurableWholesale'] = [
                'product_id' => $productId,
                'item_id' => $itemId,
                'urlLoadItem' => $subject->getUrl('configurablewholesale/index/loaditem')
            ];
        }
        return $result;
    }
}
