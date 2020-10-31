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

define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    $.widget('bss.storecredit', {
        options: {
            bssStoreCreditValue : '#bss-store-credit-value',
            bssStoreCreditRemove : '#remove-bss-store-credit',
            bssStoreCreditApply : 'button.action.bss-store-credit-apply',
            bssStoreCreditCancel : 'button.action.bss-store-credit-cancel'
        },
        _create: function () {
            this.storeCreditValue = $(this.options.bssStoreCreditValue);
            this.storeCreditRemove = $(this.options.bssStoreCreditRemove);

            $(this.options.bssStoreCreditApply).on('click', $.proxy(function () {
                this.storeCreditValue.attr('data-validate', '{required:true}');
                this.storeCreditRemove.attr('value', '0');
                $(this.element).validation().submit();
            }, this));

            $(this.options.bssStoreCreditCancel).on('click', $.proxy(function () {
                this.storeCreditValue.removeAttr('data-validate');
                this.storeCreditRemove.attr('value', '1');
                this.element.submit();
            }, this));
        }
    });

    return $.bss.storecredit;
});
