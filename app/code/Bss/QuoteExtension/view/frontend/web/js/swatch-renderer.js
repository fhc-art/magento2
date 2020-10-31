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
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _) {
    'use strict';
    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {

            /**
             * Event for swatch options
             *
             * @param {Object} $this
             * @param {Object} $widget
             * @private
             */
            _OnClick: function ($this, $widget) {

                $widget._super($this, $widget);

                $widget._UpdateRequestQuote($this);
            },

            /**
             * Event for select
             *
             * @param {Object} $this
             * @param {Object} $widget
             * @private
             */
            _OnChange: function ($this, $widget) {

                $widget._super($this, $widget);

                $widget._UpdateRequestQuote();
            },

            _UpdateRequestQuote: function () {
                var $widget = this,
                    index = '',
                    childProductData = this.options.jsonConfig.quoteExtension;
                    
                if (typeof childProductData !== "undefined") {
                    var element = '.quote_extension' + childProductData['entity'] + ' .action.toquote';
                    $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                        index += $(this).attr('option-selected') + '_';
                    });

                    if (!childProductData['child'].hasOwnProperty(index)) {
                        $widget._DisableButton(element);
                        return false;
                    }
                    if (childProductData['child'][index]['enable']) {
                        $widget._EnableButton(element);
                    } else {
                        $widget._DisableButton(element);
                    }
                }
            },

            _DisableButton: function (element) {
                $(element).attr('disabled','disabled');
            },

            _EnableButton: function (element) {
                $(element).removeAttr('disabled');
            }
        });

        return $.mage.SwatchRenderer;
    }
});
