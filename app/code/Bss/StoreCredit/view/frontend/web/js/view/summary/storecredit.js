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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/cart/cache'
    ],
    function (Component, totals, cartCache) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Bss_StoreCredit/summary/storecredit'
            },
            totals: totals.totals(),
            getPureValue: function () {
                var price = 0;
                cartCache.clear('cart-data');
                if (totals && totals.getSegment('bss_storecredit')) {
                    price = parseFloat(totals.getSegment('bss_storecredit').value);
                }
                return price;
            },
            isDisplayed: function () {
                if (this.isFullMode() && this.getPureValue() != 0) {
                    return true;
                }
                return false;
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            }

        });
    }
);
