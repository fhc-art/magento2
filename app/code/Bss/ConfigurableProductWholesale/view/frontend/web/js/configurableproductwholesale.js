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

define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'mage/translate',
    'underscore',
    'jquery/ui'
], function ($, priceUtils, $t, _) {
    "use strict";
    $.widget('bss.configurableproductwholesale', {
        options: {
            jsonChildInfo: {},
            fomatPrice: '',
            jsonPriceTableOrdering: {},
            jsonSwatchConfig: {},
            jsonSystemConfig: {},
            classes: {
                optionClass: 'swatch-option',
                selectClass: 'super-attribute-select',
                swatchSelectClass: 'swatch-select',
                qtyClass: 'bss-qty'
            },
            values: {
                totals: [],
                exclTaxTotals: [],
                totalsQty: []
            },
            ids: {
                wrapper: 'product-options-wrapper',
                optionPrice: 'bss-option-price'
            },
            selectorTableRow: '.bss-table-row',
            html : []
        },

        _init: function () {
            if (this.options.jsonPriceTableOrdering.finalPrice !== '' && this.options.jsonChildInfo !== '') {
                this._EventListener();
            } else {
                console.log('Bss Table Ordering : Error');
            }
            var self = this;
            // this function will be called to reset old data after Add to Cart action
            window.resetCPWValues = function() {
                self.options.values = {
                    totals: [],
                    exclTaxTotals: [],
                    totalsQty: []
                };
                $('#bss-ptd-table').find('tfoot .bss-detailed-total').parents('tr').remove();
            }
        },

        _EventListener: function () {
            var self = this,
                opt = self.options;

            $('#' + opt.ids.wrapper).on('click','.' + self.options.classes.optionClass, function () {
                if ($(this).hasClass('disabled') || !$(this).hasClass('selected') || $(this).hasClass('bss-table-row-attr')) {
                    return;
                }
                self._RerenderTableOrdering();
            });

            $('#' + opt.ids.wrapper).on('change','.' + self.options.classes.swatchSelectClass, function () {
                if ($(this).hasClass('disabled') || $(this).val() == 0 || $(this).hasClass('bss-table-row-attr')) {
                    return;
                }
                self._RerenderTableOrdering();
            });

            $('#' + opt.ids.wrapper).on('change','.' + self.options.classes.selectClass, function () {
                if ($(this).val() == 0 || $(this).parent().parent().hasClass('bss-last-select')) {
                    return;
                }
                $('#' + self.options.ids.wrapper + ' .super-attribute-select').each(function () {
                    var _this = this;
                    var value = $(_this).find('option:eq(1)').val();
                    if (!$(_this).val() && !$(_this).parent().parent().hasClass('bss-last-select')) {
                        $(_this).val(value);
                    }
                });
                self._RerenderTableOrdering();
            });

            if ($('#bss-no-swatch').length) {
                self._loadNoSwatch();
            }

            $('#' + opt.ids.wrapper).on('change','#' + self.options.ids.optionPrice, function () {
                var _this = this;
                $('.' + self.options.classes.qtyClass).each(function () {
                    self._CalcRowPrice($(this));
                    if ($(this).val() > 0) {
                        self._loadDetailTotal($(this));
                    }
                });

                self._reloadRangePrice();
            });
            self._RenderTableOrdering();
            if (self.options.jsonSystemConfig.textColor) {
                $('#bss-ptd-table thead th').css('color', '#'+self.options.jsonSystemConfig.textColor);
            }
            if (self.options.jsonSystemConfig.backGround) {
                $('#bss-ptd-table thead th').css('background-color', '#'+self.options.jsonSystemConfig.backGround);
            }
            $('.product-options-bottom .box-tocart .field.qty').remove();
            self.element.find('.bss-totals').html(self._getFormattedPrice(0));
            self.element.find('.bss-excltax-totals').html(self._getFormattedPrice(0));
            self.element.find('.bss-totals-qty').html(0);

            $('#' + opt.ids.wrapper).on('change','.' + self.options.classes.qtyClass, function () {
                var _this = this;
                self._CalcRowPrice($(_this));
                $('.' + self.options.classes.qtyClass).each(function () {
                    self._CalcRowPrice($(this));
                });
                self._loadDetailTotal($(_this));
            });

            self.element.on('keyup mousewheel','.' + self.options.classes.qtyClass, function () {
                self._checkError($(this));
            });

            $(this.options.selectorTableRow).hover(function () {
                if ($(this).find('.bss-tier-detailed').length > 0) {
                    $(this).find('.bss-tier-detailed').removeClass('bss-hidden');
                }
            }, function () {
                if ($(this).find('.bss-tier-detailed').length > 0) {
                    $(this).find('.bss-tier-detailed').addClass('bss-hidden');
                }
            });

            $(this.options.selectorTableRow).touchstart(function () {
                if ($(this).find('.bss-tier-detailed').length > 0) {
                    $(this).find('.bss-tier-detailed').removeClass('bss-hidden');
                }
            }, function () {
                if ($(this).find('.bss-tier-detailed').length > 0) {
                    $(this).find('.bss-tier-detailed').addClass('bss-hidden');
                }
            });
        },

        _RerenderTableOrdering: function () {
            var self = this;
            $(self.options.selectorTableRow).each(function () {
                var $this = $(this),
                    countOption = 0,
                    countTmpOption = 0,
                    countSelect = 0,
                    countTmpSelect = 0,
                    optionElement = '',
                    selectorData = $this.find('.bss-data');
                $('.normal-price > .price-final_price > .price-label').hide();
                if ($('.' + self.options.classes.optionClass).length > 0) {
                    $('.' + self.options.classes.optionClass).each(function () {
                        var id,
                            attrId;
                        if ($(this).hasClass('selected') && !$(this).hasClass('bss-table-row-attr')) {
                            id = $(this).closest('.swatch-attribute').attr('attribute-id');
                            attrId = $(this).attr('option-id');

                            if (selectorData.attr('data-option-'+id) == attrId) {
                                countTmpOption++;
                            }
                            countOption++;
                        }

                    });
                }

                if ($('#' + self.options.ids.wrapper + ' .' + self.options.classes.selectClass).length > 0) {
                    optionElement = $('#' + self.options.ids.wrapper + ' .' + self.options.classes.selectClass);
                }
                if ($('#' + self.options.ids.wrapper + ' .swatch-select').length > 0) {
                    optionElement = $('#' + self.options.ids.wrapper + ' .swatch-select');
                }
                if (optionElement) {
                    optionElement.each(function () {
                        var id,
                            attrId;
                        if ($(this).val() != 0) {
                            id = $(this).closest('.swatch-attribute').attr('attribute-id');
                            attrId = $(this).val();
                            if (selectorData.attr('data-option-'+id) == attrId) {
                                countTmpSelect++;
                            }
                            countSelect++;
                        }
                    });
                }

                if (countOption == countTmpOption && countSelect == countTmpSelect) {
                    $this.removeClass('bss-hidden');
                } else {
                    $this.addClass('bss-hidden');
                }
            });
            $('#bss-ptd-table').removeClass('bss-hidden');
        },

        _RenderTableOrdering: function () {
            var html = '',
                _this = this,
                jsonChildInfo = this.options.jsonChildInfo,
                sortOrder = 0,
                option_id = 0,
                subtotalSelector = '',
                exclTaxSubtotalSelector = '';
            $.each(jsonChildInfo, function (key3, self) {
                var qty = 0,
                    subtotalClass = '',
                    exclTaxSubtotalClass = '',
                    priceClass = '',
                    exclTaxPriceClass = '',
                    availabilityClass = '',
                    skuClass = '',
                    tierPriceClass = '',
                    mobileClass = ' bss-hidden-480',
                    tabletClass = ' bss-hidden-1024',
                    itemSelector = '',
                    disabledClass = '',
                    disabled = '',
                    detailedNote = '',
                    detailedPrice = '',
                    showQty = false,
                    productId = self.other.product_id;
                if (!self.status_stock) {
                    disabledClass = 'bss-disabled';
                    disabled = 'disabled';
                }

                if (_this.options.jsonSystemConfig.showSubTotal) {
                    if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.subtotal) {
                        subtotalClass += mobileClass;
                    }
                    if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.subtotal) {
                        subtotalClass += tabletClass;
                    }
                    subtotalSelector = '<td class="bss-subtotal-'+productId+subtotalClass+'">';
                    subtotalSelector += _this._getFormattedPrice(0)+'</td>';
                }

                if (_this.options.jsonSystemConfig.showExclTaxSubTotal) {
                    if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.excl_tax_price) {
                        exclTaxSubtotalClass += mobileClass;
                    }
                    if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.excl_tax_price) {
                        exclTaxSubtotalClass += tabletClass;
                    }
                    exclTaxSubtotalSelector = '<td class="bss-excltax-subtotal-'+productId+exclTaxSubtotalClass+'">';
                    exclTaxSubtotalSelector += _this._getFormattedPrice(0)+'</td>';
                }

                html += '<tr class="bss-table-row '+disabledClass+'">';
                $.each(self, function (key2, attrData) {

                    if (key2 == "option_id") {
                        option_id = self.option_id;
                        return true;
                    }

                    if (key2 == "sort_order") {
                        sortOrder = self.sort_order;
                        return true;
                    }

                    if (key2 === 'other' || key2 === 'tier_price' || key2 === 'status_stock' || key2 === 'attribute_code' || key2 === 'attribute_id') {
                        return;
                    }
                    if (key2 === 'option') {
                        var data = '',
                            optionId = '';
                        $.each(attrData, function (key,val) {
                            optionId = key.replace('data-option-', '');
                            data += key + '="' + val + '" ';
                            html += '<input class="product-id-'+productId+'" type="hidden" name="bss_super_attribute['+productId+']['+optionId+']" data-option-id="'+optionId+'" value="'+val+'" />';
                        });
                        html += '<input type="hidden" class="bss-data" ' + data +' value="" />';
                    } else if (key2 === 'price') {
                        showQty = attrData.saleable;
                        if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.unit_price) {
                            priceClass += mobileClass;
                        }
                        if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.unit_price) {
                            priceClass += tabletClass;
                        }

                        if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.excl_tax_price) {
                            exclTaxPriceClass += mobileClass;
                        }
                        if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.excl_tax_price) {
                            exclTaxPriceClass += tabletClass;
                        }
                        html += '<td class="bss-unitprice-'+productId+priceClass+'">';
                        html += '<div class="bss-price" data-amount="'+attrData.final_price+'">' + _this._getFormattedPrice(attrData.final_price) + '</div>';
                        if (attrData.old_price) {
                            html += '<div class="bss-old-price" data-amount="'+attrData.old_price+'">' + _this._getFormattedPrice(attrData.old_price) + '</div>';
                        }
                        html += '</td>';

                        if (_this.options.jsonSystemConfig.showExclTaxSubTotal) {
                            html += '<td class="bss-excltax-unitprice-'+productId+exclTaxPriceClass+'">';
                            html += '<div class="bss-excltax-price" data-amount="'+attrData.excl_tax_final_price+'">' + _this._getFormattedPrice(attrData.excl_tax_final_price) + '</div>';
                            if (attrData.excl_tax_old_price) {
                                html += '<div class="bss-excltax-old-price" data-amount="'+attrData.excl_tax_old_price+'">' + _this._getFormattedPrice(attrData.excl_tax_old_price) + '</div>';
                            }
                            html += '</td>';
                        }
                    } else if (key2 === 'attribute') {
                        var swatchHtml = attrData;
                        if (typeof _this.options.jsonSwatchConfig[self.attribute_id] !== 'undefined') {
                            swatchHtml = _this._RenderControls(_this.options.jsonSwatchConfig[self.attribute_id], self.option_id);
                        }
                        html += '<td class="bss-table-row-attr swatch-option swatch-attribute" attribute-id="'+self.attribute_id+'" attribute-code="'+self.attribute_code+'" option-id="'+self.option_id+'">' + swatchHtml + '</td>';
                    } else if (key2 === 'qty_stock') {
                        if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.availability) {
                            availabilityClass += mobileClass;
                        }
                        if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.availability) {
                            availabilityClass += tabletClass;
                        }
                        if (availabilityClass) {
                            html += '<td class="'+availabilityClass+'">';
                        } else {
                            html += '<td>';
                        }
                        html += attrData + '</td>';
                    } else if (key2 === 'sku') {
                        if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.sku) {
                            skuClass += mobileClass;
                        }
                        if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.sku) {
                            skuClass += tabletClass;
                        }
                        if (skuClass) {
                            html += '<td class="'+skuClass+'">';
                        } else {
                            html += '<td>';
                        }
                        html += attrData + '</td>';
                    } else {
                        html += '<td>' + attrData + '</td>';
                    }
                });

                if (self.tier_price) {
                    if (_this.options.jsonSystemConfig.mobile && !_this.options.jsonSystemConfig.mobile.tier_price) {
                        tierPriceClass += mobileClass;
                    }
                    if (_this.options.jsonSystemConfig.tablet && !_this.options.jsonSystemConfig.tablet.tier_price) {
                        tierPriceClass += tabletClass;
                    }
                    detailedPrice += '<div class="bss-tier-detailed bss-hidden'+tierPriceClass+'">';
                    detailedPrice += self.tier_price+'</div>';
                }

                if (self.other) {
                    if (self.other.min_qty) {
                        itemSelector += '<input type="hidden" class="bss-min-qty" value="'+self.other.min_qty+'" />';
                    }
                    if (self.other.max_qty) {
                        itemSelector += '<input type="hidden" class="bss-max-qty" value="'+self.other.max_qty+'" />';
                    }
                }
                detailedNote += '<div generated="true" class="bss-note-detailed bss-hidden mage-error"></div>';
                html += subtotalSelector;
                html += exclTaxSubtotalSelector;
                var inputQty = '';
                if (showQty) {
                    inputQty = '<td class="bss-qty-col">'
                    + '<input type="number" name="bss-qty['+productId+']" maxlength="12" value="'+qty+'" title="Qty" class="input-text qty bss-qty" id="bss-qty-'+productId+'" data-product-id="'+productId+'" '+disabled+'>'
                    + detailedPrice
                    + detailedNote
                    + itemSelector
                    + '</td>'
                }
                html += inputQty +'</tr>';
            });
            _this.element.append(html);
            if ($('#bss-no-swatch').length && $('#bss-no-swatch').val()) {
                _this.element.removeClass('bss-hidden');
            }
        },

        _CalcRowPrice: function ($this) {
            var productId,
                qty = 0;
            var qtyDecimal = $('#bss-qty-decimal').val();
            productId = $this.attr('data-product-id');
            if ($this.val() && $this.val() > 0) {
                qty = parseFloat($this.val());
                if (qtyDecimal == '0') {
                    qty = Math.floor(qty);
                }
            }
            this._RenderPrice(qty, productId);
            this._reloadUnitPrice($this);

        },

        _RenderPrice: function (qty, productId) {
            var totalQty = 0;
            var price = 0;
            var exclTaxPrice = 0;
            var subtotal = 0;
            var exclTaxSubtotal = 0;
            var unitPrice = 0;
            var unitExclTaxPrice = 0;
            var bssOptionPrice = parseFloat($('#bss-option-price').val());
            var bssExclTaxOptionPrice = parseFloat($('#bss-option-price').attr('data-excltax-price'));

            this.options.values.totalsQty[productId] = qty;
            totalQty = this.options.values.totalsQty.reduce(function (total, num) {
                return total + num;
            });
            this.element.find('.bss-totals-qty').html(totalQty);
            if (!_.isEmpty(this.options.jsonPriceTableOrdering.finalPrice) && !_.isEmpty(this.options.jsonPriceTableOrdering.finalPrice[productId]) ) {
                for (var key in this.options.jsonPriceTableOrdering.finalPrice[productId]) {
                    if (!this.options.jsonSystemConfig.tierPriceAdvanced && qty >= parseFloat(key)) {
                        price = parseFloat(this.options.jsonPriceTableOrdering.finalPrice[productId][key].finalPrice);
                        exclTaxPrice = parseFloat(this.options.jsonPriceTableOrdering.finalPrice[productId][key].exclTaxFinalPrice);
                    } else if (this.options.jsonSystemConfig.tierPriceAdvanced && totalQty >= parseFloat(key)) {
                        price = parseFloat(this.options.jsonPriceTableOrdering.finalPrice[productId][key].finalPrice);
                        exclTaxPrice = parseFloat(this.options.jsonPriceTableOrdering.finalPrice[productId][key].exclTaxFinalPrice);
                    }
                }
                if (price && price > 0) {
                    subtotal = (price + bssOptionPrice) * qty;
                    exclTaxSubtotal = (exclTaxPrice + bssExclTaxOptionPrice) * qty;
                    unitPrice = price;
                    unitExclTaxPrice = exclTaxPrice;
                } else {
                    unitPrice = this.options.jsonPriceTableOrdering.finalPrice[productId][1].finalPrice;
                    unitExclTaxPrice = this.options.jsonPriceTableOrdering.finalPrice[productId][1].exclTaxFinalPrice;
                }
                this.options.values.totals[productId] = subtotal;
                this.options.values.exclTaxTotals[productId] = exclTaxSubtotal;
                this.element.find('.bss-subtotal-' + productId).html(this._getFormattedPrice(subtotal));
                this.element.find('.bss-excltax-subtotal-' + productId).html(this._getFormattedPrice(exclTaxSubtotal));
                this.element.find('.bss-unitprice-' + productId + ' .bss-price').attr('data-amount', unitPrice);
                this.element.find('.bss-excltax-unitprice-' + productId + ' .bss-excltax-price').attr('data-amount', unitExclTaxPrice);
                this.element.find('.bss-totals').html(this._getFormattedPrice(this.options.values.totals.reduce(function (total, num) {
                    return total + num;
                })));
                this.element.find('.bss-excltax-totals').html(this._getFormattedPrice(this.options.values.exclTaxTotals.reduce(function (exclTaxTotal, exclTaxNum) {
                    return exclTaxTotal + exclTaxNum;
                })));
            }
            this.validateQty();
        },

        _reloadUnitPrice: function ($this) {
            if (!_.isEmpty(this.options.jsonPriceTableOrdering.finalPrice)) {
                var price,
                    unitPrice = 0,
                    exclTaxUnitPrice = 0,
                    exclTaxOptionPrice = 0,
                    exclTaxPrice,
                    oldPrice,
                    exclTaxOldPrice,
                    optionPrice,
                    self = this,
                    oldPriceElem = $this.closest('.bss-table-row').find('.bss-old-price'),
                    exclTaxOldPriceElem = $this.closest('.bss-table-row').find('.bss-excltax-old-price'),
                    element = $this.closest('.bss-table-row').find('.bss-price'),
                    exclTaxElement = $this.closest('.bss-table-row').find('.bss-excltax-price');
                optionPrice = parseFloat($('#' + self.options.ids.optionPrice).val());
                exclTaxOptionPrice = parseFloat($('#' + self.options.ids.optionPrice).attr('data-excltax-price'));
                price = parseFloat(element.attr('data-amount'));
                exclTaxPrice = parseFloat(exclTaxElement.attr('data-amount'));
                if (oldPriceElem.length > 0) {
                    oldPrice = parseFloat(oldPriceElem.attr('data-amount'));
                    oldPriceElem.html(self._getFormattedPrice(oldPrice + optionPrice));
                    exclTaxOldPrice = parseFloat(exclTaxOldPriceElem.attr('data-amount'));
                    exclTaxOldPriceElem.html(self._getFormattedPrice(exclTaxOldPrice + exclTaxOptionPrice));
                }
                if ($.isNumeric(element.attr('data-amount'))) {
                    unitPrice = self._getFormattedPrice(price + optionPrice);
                    exclTaxUnitPrice = self._getFormattedPrice(exclTaxPrice + exclTaxOptionPrice);
                    exclTaxElement.html(exclTaxUnitPrice);
                } else {
                    unitPrice = element.attr('data-amount');
                    exclTaxElement.html('');
                }
                element.html(unitPrice);
            } else {
                $('.bss-totals').parent().remove();
            }
        },

        _getFormattedPrice: function (price) {
            if ($.isNumeric(price)) {
                return priceUtils.formatPrice(price, this.options.fomatPrice);
            } else if (typeof price == "string") {
                return price;
            }
            return 0;
        },

        _reloadRangePrice: function () {
            var optionPrice,
                exclTaxOptionPrice,
                priceFrom,
                exclTaxPriceFrom,
                priceTo,
                exclTaxPriceTo,
                priceUnit,
                exclTaxPriceUnit;
            optionPrice = parseFloat($('#'+this.options.ids.optionPrice).val());
            exclTaxOptionPrice = parseFloat($('#'+this.options.ids.optionPrice).attr('data-excltax-price'));
            if ($('.bss-price-from').length > 0) {
                priceFrom = parseFloat($('.bss-price-from-value').attr('data-price-amount'));
                $('.bss-price-from-value').html(this._getFormattedPrice(priceFrom + optionPrice));
            }

            if ($('.bss-excltax-price-from').length > 0) {
                exclTaxPriceFrom = parseFloat($('.bss-excltax-price-from-value').attr('data-price-amount'));
                $('.bss-excltax-price-from-value').html(this._getFormattedPrice(exclTaxPriceFrom + exclTaxOptionPrice));
            }

            if ($('.bss-price-to').length > 0) {
                priceTo = parseFloat($('.bss-price-to-value').attr('data-price-amount'));
                $('.bss-price-to-value').html(this._getFormattedPrice(priceTo + optionPrice));
            }

            if ($('.bss-excltax-price-to').length > 0) {
                exclTaxPriceTo = parseFloat($('.bss-excltax-price-to-value').attr('data-price-amount'));
                $('.bss-excltax-price-to-value').html(this._getFormattedPrice(exclTaxPriceTo + exclTaxOptionPrice));
            }

            if ($('.bss-price-unit').length > 0) {
                priceUnit = parseFloat($('.bss-price-unit-value').attr('data-price-amount'));
                $('.bss-price-unit-value').html(this._getFormattedPrice(priceUnit + optionPrice));
            }

            if ($('.bss-excltax-price-unit').length > 0) {
                exclTaxPriceUnit = parseFloat($('.bss-excltax-price-unit-value').attr('data-price-amount'));
                $('.bss-excltax-price-unit-value').html(this._getFormattedPrice(exclTaxPriceUnit + exclTaxOptionPrice));
            }
        },

        _checkError: function ($this) {
            var mess = '',
                minQty,
                maxQty,
                qty;
            minQty = parseFloat($this.closest('.bss-qty-col').find('.bss-min-qty').val());
            maxQty = parseFloat($this.closest('.bss-qty-col').find('.bss-max-qty').val());
            qty = parseFloat($this.val());
            if (!qty && qty != 0) {
                mess += '<div>'+$t("This is a required field.")+'</div>'
            }
            if (qty && qty < 0) {
                mess += '<div>'+$t("Please enter a number 0 or greater in this field.")+'</div>'
            }
            if (minQty && qty && qty > 0 && qty < minQty) {
                mess += '<div>'+$t("The fewest you may purchase is %1.").replace('%1', minQty)+'</div>'
            }
            if (maxQty && qty && qty > maxQty) {
                mess += '<div>'+$t("The most you may purchase is %1.").replace('%1', maxQty)+'</div>'
            }
            if (mess != '') {
                $this.closest('.bss-qty-col').find('.bss-note-detailed').html(mess).removeClass('bss-hidden');
            } else {
                $this.closest('.bss-qty-col').find('.bss-note-detailed').addClass('bss-hidden');
            }
        },

        _loadDetailTotal: function ($this) {
            var self = this,
                optionConfig,
                id,
                attributeId,
                value,
                text,
                attributeIdSelect,
                optionIdSelect,
                html = '',
                detailedEl,
                qty = 0;
            var qtyDecimal = $('#bss-qty-decimal').val();
            if ($('#bss-no-swatch').length) {
                detailedEl = $('#' + self.options.ids.wrapper + ' .swatch-attribute:first');
            } else {
                detailedEl = $('[data-role=swatch-options] .swatch-attribute:first');
            }
            attributeId = detailedEl.attr('attribute-id');
            id = $this.closest('.bss-table-row').find('.bss-data').attr('data-option-'+attributeId);
            self.element.find('tbody .bss-data[data-option-'+attributeId+'='+id+']').each(function () {
                if (!$(this).closest('.bss-table-row').hasClass('bss-disabled')) {
                    var qtyCur = parseFloat($(this).closest('.bss-table-row').find('.bss-qty').val());
                    if (qtyDecimal == '0') {
                        qtyCur = Math.floor(qtyCur);
                    }
                    if (!isNaN(qtyCur) && parseFloat(qtyCur) > 0) {
                        qty += qtyCur;
                    }
                }
            });

            if (qty == 0) {
                self.element.find('tfoot [data-attribute-'+attributeId+'='+id+']').closest('tr').remove();
            } else {
                optionConfig = self.options.jsonSwatchConfig[attributeId];
                if (self.element.find('tfoot [data-attribute-'+attributeId+'='+id+']').length == 0) {
                    html += '<tr class="attr-qty"><td data-attribute-'+attributeId+'="'+id+'">';
                    if (typeof optionConfig != "undefined") {
                        html += self._RenderControls(optionConfig, id);
                    } else {
                        attributeIdSelect = detailedEl.attr('attribute-id');
                        optionIdSelect = $this.closest('.bss-table-row').find('.bss-data').attr('data-option-'+attributeIdSelect);
                        if ($('#bss-no-swatch').length) {
                            text = detailedEl.find('option[value='+optionIdSelect+']').text();
                        } else {
                            text = detailedEl.find('.swatch-select option[option-id='+optionIdSelect+']').text();
                        }
                        html += '<div>' + text + '</div>';
                    }
                    html += '</td>';
                    html += '<td class="bss-detailed-total" data-attribute-'+attributeId+'="'+id+'">'+qty+'</td>'
                    html += '</tr>';
                    if ($('#bss-no-swatch').length && $('.super-attribute-select').length == 1) {
                        return;
                    } else {
                        self.element.find('tfoot').prepend(html);
                    }
                } else {
                    self.element.find('tfoot .bss-detailed-total[data-attribute-'+attributeId+'='+id+']').text(qty);
                }
            }
        },

        _loadNoSwatch: function () {
            var self = this;
            self.element.on('click','.bss-table-row-attr', function () {
                var optionId;
                if ($(this).hasClass('selected')) {
                    $(this).removeAttr('option-selected').removeClass('selected');
                } else {
                    optionId = $(this).attr('option-id');
                    $('#bss-ptd-table').find('.selected').removeClass('selected');
                    $(this).addClass('selected');
                    $('.bss-last-select .super-attribute-select').val(optionId).change();
                }
            });
            setTimeout(function () {
                self._refreshOption();
            }, 1000);
        },

        _refreshOption: function () {
            var self = this,
                addCart,
                encodeAddCart,
                updateCart,
                encodeUpdateCart,
                editInfo = window.jsonEditInfo;
            var item_id = 0,
                product_id = 0,
                bssConfig = window.checkout.bssConfigurableWholesale;
            if (bssConfig && _.isEmpty(editInfo)) {
                item_id = bssConfig.item_id;
                product_id = bssConfig.product_id;
                $.ajax({
                    type: 'post',
                    url: bssConfig.urlLoadItem,
                    data: {product: product_id, item_id: item_id},
                    dataType: 'json',
                    success: function (data) {
                        if (data) {
                            _.each(data.product,function (option, productId) {
                                $('#bss-ptd-table .bss-qty-col #bss-qty-'+productId).val(option.qty);
                            });
                            if ($('#bss-addtocart-data').length) {
                                addCart = JSON.stringify(data.product);
                                encodeAddCart = encodeURI(addCart);
                                $('#bss-addtocart-data').val(encodeAddCart);
                                updateCart = JSON.stringify(data.item);
                                encodeUpdateCart = encodeURI(updateCart);
                                $('#bss-addtocart-data').val(encodeAddCart);
                                $('#bss-updatecart-data').val(encodeUpdateCart);
                            }
                            window.reloadDetails = true;
                            self._preSelectOption(data);
                        }
                    }
                });
            } else {
                self._preSelectOption(editInfo);
            }
        },

        _preSelectOption: function (editInfo) {
            var self = this;
            if (_.isEmpty(editInfo)) {
                if ($('#' + self.options.ids.wrapper + ' .super-attribute-select').size() == 1) {
                    self._RerenderTableOrdering();
                } else {
                    $('#' + self.options.ids.wrapper + ' .super-attribute-select').each(function () {
                        var _this = this;
                        var value = $(_this).find('option:eq(1)').val();
                        if (!$(_this).closest('.field.configurable').hasClass('bss-last-select')) {
                            $(_this).val(value).change();
                        }
                    });
                }
            } else {
                var ele = $('#' + self.options.ids.wrapper + ' .bss-last-select').prev().find('.super-attribute-select');
                if (ele) {
                    ele.change();
                }
                if ($('.product-custom-option').length > 0) {
                    $('.product-custom-option').change();
                } else {
                    $('.bss-qty').change();
                }
            }
        },

        _RenderControls: function (optionConfig, id) {
            var self = this,
                html = '',
                type = parseInt(optionConfig[id].type, 10),
                value = optionConfig[id].hasOwnProperty('value') ? optionConfig[id].value : '',
                optionClass = self.options.classes.optionClass,
                label = optionConfig[id].label ? optionConfig[id].label : '',
                attr =
                    ' option-type="' + type + '"' +
                    ' option-id="' + id + '"' +
                    ' option-label="' + label + '"';
            if (type === 0) {
                // Text
                html += '<div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                    '</div>';
            } else if (type === 1) {
                // Color
                html += '<div class="' + optionClass + ' color" ' + attr +
                    '" style="background: ' + value +
                    ' no-repeat center; background-size: initial;">' + '' +
                    '</div>';
            } else if (type === 2) {
                // Image
                html += '<div class="' + optionClass + ' image" ' + attr +
                    '" style="background: url(' + value + ') no-repeat center; background-size: initial;">' + '' +
                    '</div>';
            } else if (type === 3) {
                // Clear
                html += '<div class="' + optionClass + '" ' + attr + '></div>';
            } else {
                // Default
                html += '<div class="' + optionClass + '" ' + attr + '>' + label + '</div>';
            }
            return html;
        },
        validateQty: function () {
            var error = false,
                increment = $('#bss-qty-increment').length > 0 ? Number($('#bss-qty-increment').val()) : 1;
            if (increment > 1) {
                $('.bss-qty').each(function () {
                    var qty = Number($(this).val())
                    if (qty > 0 && qty % increment !== 0) {
                        error = true;
                    }
                });
            }
            if (error) {
                $('#product-addtocart-button').attr('disabled', 'disabled');
            } else {
                $('#product-addtocart-button').removeAttr('disabled');
            }
        }
    });
    return $.bss.configurableproductwholesale;
});
