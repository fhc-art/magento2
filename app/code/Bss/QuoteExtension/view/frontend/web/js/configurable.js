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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'underscore',
], function ($) {
    'use strict';
    return function (widget, _) {

        $.widget('mage.configurable', widget, {

            /**
             * Configure an option, initializing it's state and enabling related options, which
             * populates the related option's selection and resets child option selections.
             * @private
             * @param {*} element - The element associated with a configurable option.
             */
            _configureElement: function (element) {
                this._super(element);

                this._UpdateRequestQuote();
            },

            _UpdateRequestQuote: function () {
                var $widget = this,
                    index = '',
                    element = '.action.toquote',
                    childProductData = this.options.spConfig.quoteExtension;
                $(".super-attribute-select").each(function () {
                    var option_id = $(this).attr("option-selected");
                    if (typeof option_id === "undefined" && $(this).val() !== "") {
                        option_id = $(this).val();
                    }
                    if (option_id !== null && $(this).val() !== "") {
                        index += option_id + '_';
                    }
                });
            },

            _DisableButton: function (element) {
                $(element).attr('disabled','disabled');
            },

            _EnableButton: function (element) {
                $(element).removeAttr('disabled');
            }
        });

        return $.mage.configurable;
    }
});
