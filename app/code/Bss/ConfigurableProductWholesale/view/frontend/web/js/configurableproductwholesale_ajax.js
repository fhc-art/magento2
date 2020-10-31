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
    'underscore',
    'Magento_Catalog/js/price-utils',
    'bss/configurableproductwholesale',
], function ($, _, priceUtils) {
    "use strict";
    $.widget('bss.configurableproductwholesale_ajax', $.bss.configurableproductwholesale, {

        _EventListener: function () {
            var self = this,
                opt = self.options;

            $('#' + opt.ids.wrapper).on('change','.' + self.options.classes.swatchSelectClass, function () {
                if ($(this).hasClass('disabled') || $(this).val() == 0 || $(this).hasClass('bss-table-row-attr')) {
                    return;
                }
                self._RerenderTableOrdering($(this));
            });

            $('#' + opt.ids.wrapper + ',#product_addtocart_form').on('change','.' + self.options.classes.selectClass, function () {
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
                self._RerenderTableOrdering($(this));
                return false;
            });

            if ($('#bss-no-swatch').length) {
                self._loadNoSwatch();
            }

            $('#' + opt.ids.wrapper).on('change','#' + self.options.ids.optionPrice, function () {
                var _this = this;
                window.reloadDetails = true;
                self._loadDetailTotal();
                self._CalcRowPrice();
                self._reloadRangePrice();
            });

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
                if ($(_this).val() > 0) {
                    $(_this).addClass('bss-qty-active');
                } else {
                    $(_this).removeClass('bss-qty-active');
                }
                if (self.options.jsonSystemConfig.tierPriceAdvanced) {
                    $('.' + self.options.classes.qtyClass).each(function () {
                        $(this).addClass('bss-qty-active');
                    });
                }
                self._addToCartData($(_this));
                self._loadDetailTotal($(_this));
                self._CalcRowPrice();
            });

            self.element.on('keyup mousewheel','.' + self.options.classes.qtyClass, function () {
                self._checkError($(this));
            });

            $(document).on('mouseout', this.options.selectorTableRow, function () {
                if ($(this).find('.bss-tier-detailed').length > 0) {
                    $(this).find('.bss-tier-detailed').removeClass('bss-hidden');
                }
            });
            $(document).on('mouseleave', this.options.selectorTableRow, function () {
                if ($(this).find('.bss-tier-detailed').length > 0) {
                    $(this).find('.bss-tier-detailed').addClass('bss-hidden');
                }
            });

            $(document).on('touchstart',this.options.selectorTableRow, function(){
                $(self.options.selectorTableRow).find('.bss-tier-detailed').addClass('bss-hidden');
                if ($(this).find('.bss-tier-detailed.bss-hidden').length > 0) {
                    $(this).find('.bss-tier-detailed').removeClass('bss-hidden');
                }
            });

            if (opt.countCpAttributes == 1) {
                self._RerenderTableOrdering();
            }
        },

        _RerenderTableOrdering: function ($this) {
            var self = this;
            self._LoadTableData($this);
        },

        _LoadTableData: function ($this) {
            var options = {},
                data = [],
                self = this,
                isAjax = true,
                id = $('#product_addtocart_form input[name=product]').val();
            options.productId = id;
            $('.normal-price > .price-final_price > .price-label').hide();
            $('#product_addtocart_form .super-attribute-select').each(function () {
                if (!$(this).closest('div.field').hasClass('bss-last-select')) {
                    if ($(this).val()) {
                        var attrId = $(this).closest('.swatch-attribute').attr('attribute-id');
                        var attrVal = $(this).closest('.swatch-attribute').attr('option-selected');
                        if (!attrVal) {
                            attrVal = $(this).val();
                        }
                        if ($(this).attr('name') != '') {
                            attrId = $(this).attr('name').replace(/^\D+|\D+$/g, "");
                        }
                        data.push(attrId + '_' + attrVal);
                    } else {
                        isAjax = false;
                    }
                }
            });
            if (isAjax) {
                options.option = data;
                $('div.bss-ptd-table').addClass('bss-cwd-spinner');
                $.ajax({
                    type: 'post',
                    url: self.options.jsonSystemConfig.ajaxLoadUrl,
                    data: {options: JSON.stringify(options)},
                    dataType: 'json',
                    success: function (data) {
                        self._addQtyToData(data);
                        self._RenderTableOrdering();
                        self._loadDetailTotal();
                        self._CalcRowPrice();
                        $('div.bss-ptd-table').removeClass('bss-cwd-spinner');
                    }
                });
            }
        },

        _RenderTableOrdering: function () {
            var html = '',
                _this = this,
                sortOrder = 0,
                option_id = 0,
                subtotalSelector = '',
                exclTaxSubtotalSelector = '',
                jsonChildInfo = this.options.jsonChildInfo;
            $.each(jsonChildInfo, function (key3, self) {
                var qty = 0,
                    subtotalClass = '',
                    qtyClass = '',
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
                        });
                        html += "<input type='hidden' class='bss-data' " + data + " value=''/>";
                    } else if (key2 === 'price') {
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
                    if (self.other.qty) {
                        qty = self.other.qty;
                    }
                }
                detailedNote += '<div generated="true" class="bss-note-detailed bss-hidden mage-error"></div>';

                if (qty) {
                    qtyClass += ' bss-qty-active'
                }
                html += subtotalSelector;
                html += exclTaxSubtotalSelector;
                var inputQty = '';
                if (self.price && self.price.saleable) {
                    inputQty = '<td class="bss-qty-col">'
                        + '<input type="number" name="bss-qty['+productId+']" maxlength="12" value="'+qty+'" title="Qty" class="input-text qty bss-qty" id="bss-qty-'+productId+'" data-product-id="'+productId+'" '+disabled+'>'
                        + detailedPrice
                        + detailedNote
                        + itemSelector
                        + '</td>'
                } else {
                    inputQty = '';
                }
                html += inputQty +'</tr>';
            });
            _this.element.find('tbody').empty().append(html);
            if ($('#bss-no-swatch').length && $('#bss-no-swatch').val()) {
                _this.element.removeClass('bss-hidden');
            }
        },

        _CalcRowPrice: function ($this) {
            var self = this;
            if ($this) {
                this._super($this);
            } else {
                var addCartJson = self._decodeAddData();
                var totalQty = 0;
                var price = 0;
                var bssOptionPrice = parseFloat($('#bss-option-price').val());
                var bssExclTaxOptionPrice = parseFloat($('#bss-option-price').attr('data-excltax-price'));
                $.each(addCartJson, function (productId) {
                    var qty = parseFloat(addCartJson[productId]['qty']);
                    self._RenderPrice(qty, productId);
                });
                $('.' + self.options.classes.qtyClass).each(function () {
                    var productId_z = $(this).attr('data-product-id');
                    var qty_z = 0;
                    var qtyDecimal = $('#bss-qty-decimal').val();
                    if ($(this).val() && $(this).val() > 0) {
                        qty_z = parseFloat($(this).val());
                        if (qtyDecimal == '0') {
                            qty_z = Math.floor(qty_z);
                        }
                    }
                    self._RenderPrice(qty_z, productId_z);
                    self._reloadUnitPrice($(this));
                });
            }
        },

        _loadDetailTotal: function ($this) {
            var self = this,
                optionConfig,
                id,
                decodeAddCart,
                addCartJson,
                attributeId,
                value,
                text,
                attributeIdSelect,
                optionIdSelect,
                detailedEl,
                qty = 0,
                option = {},
                addCart = $('#bss-addtocart-data').val();
            var qtyDecimal = $('#bss-qty-decimal').val();
            if ($('#bss-no-swatch').length) {
                detailedEl = $('#' + self.options.ids.wrapper + ' .swatch-attribute:first');
            } else {
                detailedEl = $('[data-role=swatch-options] .swatch-attribute:first');
            }
            attributeId = detailedEl.attr('attribute-id');
            var attrQty = self.options.countCpAttributes;
            if ($this) {
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
                if (attrQty <= 2) {
                    self._RenderDetails(qty, attributeId, id, detailedEl, $this);
                }
            } else {
                if (addCart && window.reloadDetails) {
                    addCartJson = self._decodeAddData();
                    $.each(addCartJson, function (productId) {
                        id = addCartJson[productId]['data']['data-option-'+attributeId];
                        if (typeof option[id] == "undefined") {
                            option[id] = parseFloat(addCartJson[productId]['qty']);
                        } else {
                            option[id] = option[id] + parseFloat(addCartJson[productId]['qty']);
                        }
                    });
                    $.each(option, function (optionId) {
                        if (attrQty <= 2) {
                            self._RenderDetails(option[optionId], attributeId, optionId, detailedEl);
                        }
                    });
                    window.reloadDetails = false;
                }
            }
        },

        _RenderDetails: function (qty, attributeId, id, detailedEl, $this) {
            var self = this,
                optionConfig,
                attributeIdSelect,
                optionIdSelect,
                text,
                html;

            if (qty == 0) {
                self.element.find('tfoot [data-attribute-'+attributeId+'='+id+']').closest('tr').remove();
            } else {
                optionConfig = self.options.jsonSwatchConfig[attributeId];
                if (self.element.find('tfoot [data-attribute-'+attributeId+'='+id+']').length == 0) {
                    html += '<tr><td data-attribute-'+attributeId+'="'+id+'">';
                    if (typeof optionConfig != "undefined") {
                        html += self._RenderControls(optionConfig, id);
                    } else {
                        if ($('#bss-no-swatch').length) {
                            text = detailedEl.find('option[value='+id+']').text();
                        } else {
                            text = detailedEl.find('.swatch-select option[option-id='+id+']').text();
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

        _addQtyToData: function (data) {
            var addCartJson = {},
                decodeAddCart,
                addCart = $('#bss-addtocart-data').val();
            if (addCart) {
                addCartJson = this._decodeAddData();
                $.each(addCartJson, function (productId) {
                    var qty = addCartJson[productId]['qty'];
                    var option = _.filter(data, function (optionData, index) {
                        if (optionData.other.product_id == productId) {
                            data[index]['other']['qty'] = qty;
                        }
                    });
                });
            }
            this.options.jsonChildInfo = data;
        },

        _addToCartData: function ($this) {
            var addCart,
                productId = $this.data('product-id'),
                addCartJson = {},
                decodeAddCart,
                encodeAddCart,
                option,
                self = this;
            option = _.filter(self.options.jsonChildInfo, function (data) {
                return data.other.product_id == productId;
            });
            if (option) {
                addCartJson = self._decodeAddData();
                if ($this.val() > 0) {
                    addCartJson[productId] = {};
                    addCartJson[productId]['qty'] = $this.val();
                    addCartJson[productId]['data'] = option[0]['option'];
                } else {
                    if (addCartJson) {
                        delete addCartJson[productId];
                    }
                }
                if (addCartJson) {
                    addCart = JSON.stringify(addCartJson);
                    encodeAddCart = encodeURI(addCart);
                    $('#bss-addtocart-data').val(encodeAddCart);
                }
            }
        },

        _decodeAddData: function () {
            var decodeAddCart,
                addCartJson = {},
                addCart = $('#bss-addtocart-data').val();
            if (addCart) {
                decodeAddCart = decodeURI(addCart);
                addCartJson = JSON.parse(decodeAddCart);
            }
            return addCartJson;
        }
    });
    return $.bss.configurableproductwholesale_ajax;
});