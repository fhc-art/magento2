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
    'jquery/ui',
    'mage/SwatchRenderer'
], function ($, _) {

    $.widget('bss.SwatchRenderer', $.mage.SwatchRenderer, {
        _EventListener: function () {
            var $widget = this;
            this._super();
            $(document).on('click','.bss-table-row-attr', function () {
                var optionId,
                    productId;
                if ($(this).hasClass('selected')) {
                    $(this).removeAttr('option-selected').removeClass('selected');
                } else {
                    optionId = $(this).attr('option-id');
                    $('#bss-ptd-table').find('.selected').removeAttr('option-selected').removeClass('selected');
                    $(this).attr('option-selected', optionId);
                    $(this).addClass('selected');
                    productId = $(this).closest('.bss-table-row').find('.bss-qty-col .bss-qty').attr('data-product-id');
                    return $widget._loadMedia(productId);
                }
            });
        },

        _RenderControls: function () {
            var $widget = this;
            this._super();
            if ($('#bss-table-ordering').val()) {
                var item_id = 0,
                    product_id = 0,
                    encodeAddCart,
                    addCart,
                    encodeUpdateCart,
                    updateCart,
                    bssConfig = window.checkout.bssConfigurableWholesale;
                if (bssConfig) {
                    item_id = bssConfig.item_id;
                    product_id = bssConfig.product_id;
                }
                if (item_id && product_id) {
                    $.ajax({
                        type: 'post',
                        url: bssConfig.urlLoadItem,
                        data: {product: product_id, item_id: item_id},
                        dataType: 'json',
                        success: function (data) {
                            window.jsonEditInfo = data;
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
                            $widget._UpdateQty();
                        },
                        error: function () {
                            window.jsonEditInfo = '';
                            $widget._UpdateQty(false);
                        }
                    });
                } else {
                    setTimeout(function () {
                        $widget._UpdateSwatch();
                    }, 1000);
                }
            }
        },

        _UpdateSwatch: function () {
            $('.swatch-opt .swatch-attribute .swatch-attribute-options').each(function () {
                var _this = this;
                $(_this).find('.swatch-option').each(function () {
                    if (!$(this).hasClass('disabled')) {
                        $(this).trigger('click');
                        return false;
                    }
                });
                $(_this).find('.swatch-select').each(function () {
                    var value = $(this).find('option:eq(1)').val();
                    $(this).val(value).trigger('change');
                    return false;
                });
            });
            if ($('.product-custom-option').length > 0) {
                $('.product-custom-option').trigger('change');
            } else {
                $('.bss-qty').trigger('change');
            }
            $('#bss-ptd-table').removeClass('bss-hidden');
        },

        _UpdateQty: function () {
            var $widget = this,
                editInfo = window.jsonEditInfo;
            if (_.isEmpty(editInfo)) {
                $widget._UpdateSwatch();
            } else {
                var productId = editInfo.default,
                    items = editInfo.product;
                if ($('#bss-update-data').length == 0) {
                    _.each(items, function (option, itemId) {
                        var html = '<input type="hidden" name="bss-item['+itemId+']" value="'+editInfo.item[itemId]+'" />'
                        $('#bss-qty-'+itemId).val(option.qty);
                        $('#bss-qty-'+itemId).closest('.bss-qty-col').append(html);
                    });
                }
                _.each(items[productId]['data'], function (attributeVal, attribute) {
                    var select,
                        attributeId = attribute.replace('data-option-', '');
                    $('.swatch-attribute[attribute-id='+attributeId+']').find('.swatch-option[option-id='+attributeVal+']').trigger('click');
                    select = $('.swatch-attribute[attribute-id='+attributeId+']').find('.swatch-select [option-id='+attributeVal+']').val();
                    if (select) {
                        $('.swatch-attribute[attribute-id='+attributeId+']').find('.swatch-select').val(select).trigger('change');
                    }
                });
                if ($('.product-custom-option').length > 0) {
                    $('.product-custom-option').trigger('change');
                } else {
                    $('.bss-qty').trigger('change');
                }
            }
            $('#bss-ptd-table').removeClass('bss-hidden');
        },

        _LoadProductMedia: function () {
            var $widget = this,
                $this = $widget.element,
                attributes = {},
                productId = 0,
                mediaCallData,
                mediaCacheKey,
                element,

                /**
                 * Processes product media data
                 *
                 * @param {Object} data
                 * @returns void
                 */
                mediaSuccessCallback = function (data) {
                    if (!(mediaCacheKey in $widget.options.mediaCache)) {
                        $widget.options.mediaCache[mediaCacheKey] = data;
                    }
                    $widget._ProductMediaCallback($this, data);
                    $widget._DisableProductMediaLoader($this);
                };

            if (!$widget.options.mediaCallback) {
                return;
            }
            if ($('#bss-table-ordering').val()) {
                element = $('#product_addtocart_form');
            } else {
                element = $this;
            }
            element.find('[option-selected]').each(function () {
                var $selected = $(this);
                attributes[$selected.attr('attribute-code')] = $selected.attr('option-selected');
            });

            if ($('body.catalog-product-view').size() > 0) {
                //Product Page
                productId = document.getElementsByName('product')[0].value;
            } else {
                //Category View
                productId = $this.parents('.product.details.product-item-details')
                    .find('.price-box.price-final_price').attr('data-product-id');
            }

            mediaCallData = {
                'product_id': productId,
                'attributes': attributes,
                'additional': $.parseQuery()
            };
            mediaCacheKey = JSON.stringify(mediaCallData);

            if (mediaCacheKey in $widget.options.mediaCache) {
                mediaSuccessCallback($widget.options.mediaCache[mediaCacheKey]);
            } else {
                mediaCallData.isAjax = true;
                $widget._XhrKiller();
                $widget._EnableProductMediaLoader($this);
                $widget.xhr = $.post(
                    $widget.options.mediaCallback,
                    mediaCallData,
                    mediaSuccessCallback,
                    'json'
                ).done(function () {
                    $widget._XhrKiller();
                });
            }
        },

        _UpdatePrice: function () {
            var $widget = this,
                $product = $widget.element.parents($widget.options.selectorProduct),
                $productPrice = $product.find(this.options.selectorProductPrice),
                options = _.object(_.keys($widget.optionsMap), {}),
                result;

            $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                var attributeId = $(this).attr('attribute-id');

                options[attributeId] = $(this).attr('option-selected');
            });

            result = $widget.options.jsonConfig.optionPrices[_.findKey($widget.options.jsonConfig.index, options)];
            // turn of update price box
            // $productPrice.trigger(
            //     'updatePrice',
            //     {
            //         'prices': $widget._getPrices(result, $productPrice.priceBox('option').prices)
            //     }
            // );
            // if (result && this.options.slyOldPriceSelector) {
            //     if (result.oldPrice.amount !== result.finalPrice.amount) {
            //         $(this.options.slyOldPriceSelector).show();
            //     } else {
            //         $(this.options.slyOldPriceSelector).hide();
            //     }
            // }
        },

        /**
         * Load media gallery using ajax or json config.
         *
         * @private
         */
        _loadMedia: function (productId) {
            var $main = this.inProductList ?
                    this.element.parents('.product-item-info') :
                    this.element.parents('.column.main'),
                images,
                self = this;

            if (this.options.useAjax) {
                this._debouncedLoadProductMedia();
            } else {
                if (!productId) {
                    productId = this.getProduct();
                }
                images = this.options.jsonConfig.images[productId];

                if (!images) {
                    images = this.options.mediaGalleryInitial;
                }
                setTimeout(function () {
                    self.updateBaseImage(images, $main, !self.inProductList);
                }, 500);
            }
        }
    });
    return $.bss.SwatchRenderer;
});
