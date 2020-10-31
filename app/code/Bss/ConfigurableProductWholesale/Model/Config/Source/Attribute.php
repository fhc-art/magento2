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

namespace Bss\ConfigurableProductWholesale\Model\Config\Source;

/**
 * Class Attribute
 *
 * @package Bss\ConfigurableProductWholesale\Model\Config\Source
 */
class Attribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of attribute config
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'sku', 'label' => __('Sku')],
            ['value' => 'availability', 'label' => __('Availability')],
            ['value' => 'unit_price', 'label' => __('Unit Price')],
            ['value' => 'subtotal', 'label' => __('Subtotal')],
            ['value' => 'tier_price', 'label' => __('Tier Price')],
            ['value' => 'excl_tax_price', 'label' => __('Excluding Tax Price')]
        ];
    }
}
