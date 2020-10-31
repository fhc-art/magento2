define([
    'jquery',
    'mage/translate',
    'Magento_Catalog/js/price-utils',
    'jquery/ui'
], function ($, $t, priceUtils) {
    'use strict';
    return function (widget) {

        $.widget('mage.catalogAddToCart', widget, {
            ajaxSubmit: function (form) {
                this._super(form);
                var fotmattedPrice = this._getFormattedPrice(0);
                if ($('#bss-ptd-table').length) {
                    setTimeout(function () {
                        $('#bss-ptd-table').find('.bss-totals-qty').html(0);
                        $('#bss-ptd-table').find('.bss-totals').html(fotmattedPrice);
                        $('#bss-ptd-table').find('.bss-excltax-totals').html(fotmattedPrice);
                        $('#bss-ptd-table').find('.attr-qty').remove();
                        $('.bss-qty').val(0);
                        $('#bss-addtocart-data').val('');
                        window.resetCPWValues();
                    }, 2000);
                }
            },

            _getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, '');
            },
        });

        return $.mage.catalogAddToCart;
    }
});
