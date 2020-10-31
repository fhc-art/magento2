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
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'Bss_LayerNavigation/js/submit_layered',
    'Magento_Catalog/js/price-utils',
    'jquery/ui',
    'accordion',
    'underscore',
    'productListToolbarForm',
    'bssChosen',
    'uiTouchPunch'
], function ($, submitFilterAction, ultil) {
    "use strict";

    $.widget('bss.layer', $.mage.accordion, {
        options: {
            openedState: 'active',
            collapsible: true,
            multipleCollapsible: true,
            animate: 200,
            mobileShopbyElement: '#layered-filter-block .filter-title [data-role=title]',
            collapsibleElement: '[data-role=bss_collapsible]',
            header: '[data-role=bss_title]',
            content: '[data-role=bss_content]',
            params: [],
            active: [],
            sliderElementPrefix: '#bss_slider_',
            sliderTextElementPrefix: '#bss_slider_text_',
            scroll: false,
            buttonSubmit: true,
            buttonSubmitElement: '#bss_button_submit',
            checkboxEl: 'input[type=checkbox]',
            sliderRangeElementPrefix: '#bss_slider_range_',
            sliderFromElementPrefix: '#bss_slider_from_',
            sliderToElementPrefix: '#bss_slider_to_'
        },

        _create: function () {
            var useAjax = this.options.useAjax;
            this.initActiveItems();

            this._super();

            this.initProductListUrl(useAjax);
            this.initObserve(useAjax);
            this.initSlider();
        },

        initActiveItems: function () {
            var layerActives = this.options.active,
                actives = [];

            if (typeof window.layerActiveTabs !== 'undefined') {
                layerActives = window.layerActiveTabs;
            }
            if (layerActives.length) {
                this.element.find('.filter-options-item').each(function (index) {
                    if (~$.inArray($(this).attr('attribute'), layerActives)) {
                        actives.push(index);
                    }
                });
            }

            this.options.active = actives;

            return this;
        },

        initObserve: function (useAjax) {
            var self = this;
            
            this.dropdownBox();

            this.initApplyFilterButton();
            var numberMoreLess = this.options.lessMore.status;
            var disableMoreLess = this.options.lessMore.disable;

            if (numberMoreLess > 0) {
                this.showMoreLess(numberMoreLess, disableMoreLess);
            }

            var currentElements = this.element.find('.filter-current a, .filter-actions a');
            currentElements.each(function (index) {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    if (useAjax == 0) {
                        window.location = link;
                        return;
                    }

                    submitFilterAction(link);
                    e.stopPropagation();
                    e.preventDefault();
                });
            });

            var optionElements = this.element.find('.filter-options a');
            optionElements.each(function (index) {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                if (el.attr("class") !== "bss_radio") {
                    el.bind('click', function (e) {
                        if (el.hasClass('swatch-option-link-layered')) {
                            self.selectSwatchOption(el);
                        } else {
                            var checkboxEl = el.find(self.options.checkboxEl);
                            checkboxEl.prop('checked', !checkboxEl.prop('checked'));
                        }

                        self.ajaxSubmit(link);
                        e.stopPropagation();
                        e.preventDefault();
                    });
                }

                var checkbox = el.find(self.options.checkboxEl);
                checkbox.bind('click', function (e) {
                    self.ajaxSubmit(link);
                    e.stopPropagation();
                    setTimeout(function(event) {
                        $(e.currentTarget).prop('checked', !$(e.currentTarget).prop('checked'));
                    }, 100);
                    
                    return false;
                });

                if (el.attr("class") == "bss_radio") {
                    var radio = el;
                    radio.bind('click', function (e) {
                        self.ajaxSubmit(link);
                        e.stopPropagation();
                        setTimeout(function(event) {
                            var targetAdd = $(e.currentTarget).parent().find("input[type='radio']");
                            targetAdd.attr('checked',true);
                        }, 100);
                        
                        return false;
                    });
                }
            });

            var swatchElements = this.element.find('.swatch-attribute');
            swatchElements.each(function (index) {
                var el = $(this);
                var attCode = el.attr('attribute-code');
                if (attCode) {
                    if (self.options.params.hasOwnProperty(attCode)) {
                        var attValues = self.options.params[attCode].split("_");
                        var swatchOptions = el.find('.swatch-option');
                        swatchOptions.each(function (option) {
                            var elOption = $(this);
                            if ($.inArray(elOption.attr('option-id'), attValues) !== -1) {
                                elOption.addClass('selected');
                            }
                        });
                    }
                }
            });


            var swatch = this.options.swatchOptionText;
            if (swatch.length) {
                swatch.forEach(function (value, index) {
                    self.element.find('[attribute=' + value + '] .swatch-option').each(function () {
                        var label = $(this).attr('option-label');
                        $(this).parent().addClass('swatch-option-label-layered')
                            .append("<span class='swatch-option-text'>" + label + "</span>");
                    });
                });
            }

            var optionElements = this.element.find('.filter-options a.rating-summary');
            optionElements.each(function (index) {
                $(this).bind('click', function (e) {
                    optionElements.removeClass('selected');
                    $(this).addClass('selected');
                });
            });
        },

        dropdownBox: function () {
            var self = this,
                select = this.element.find('.layer_filter_select');

            select.each(function (index) {
                var disableSearch = ($(this).attr('search') === 'false');

                $(this).chosen({
                    width: "100%",
                    disable_search: disableSearch,
                    enable_split_word_search: false,
                    display_selected_options: false,
                    allow_single_deselect: true
                });
                
                $(this).bind('change', function (e, params) {
                    var url = $(this).attr('url');
                    if (typeof params === 'object') {
                        url = params.hasOwnProperty('deselected') ? params.deselected : params.selected;
                    } else {
                        var valSelected = $(this).children('option:selected').val();
                        if (valSelected.trim() !== '') url = valSelected;
                    }
                    if (self.checkUrl(url)) {
                        self.ajaxSubmit(url);
                    }
                    e.stopPropagation();
                });
            });
        },

        showMoreLess: function(numberShow, disableMoreLess) {
            this.element.find('.filter-options-item').each(function (index) {
                var attributeCode = $(this).attr("attribute");

                if ($.inArray(attributeCode, disableMoreLess) != -1) {

                } else {

                    var countItem = 0;
                    var buttonElement = $(this).find('button#bss_show_more');

                    var itemElement = $(this).find('li.item');

                    itemElement.each(function (item) {
                        countItem = item;
                        if(item >= numberShow) {
                            $(this).hide();
                        }
                    });
                    if (countItem >= numberShow) {
                        $(this).find('button#bss_show_more').show(300);
                    }

                    buttonElement.bind('click', function (e) {
                        var buttonClick = $(this);
                        var buttonStatus = buttonElement.attr("code");
                        $(this).parent().find('li.item').each(function (item) {
                            if (buttonStatus == 'more') {
                                buttonClick.html("Show Less");
                                $(this).show(300);
                                buttonElement.attr("code","less");
                            } else {
                                buttonClick.html("Show More");
                                itemElement.each(function (item) {
                                    countItem = item;
                                    if(item >= numberShow) {
                                        $(this).hide(300);
                                    }
                                });
                                buttonElement.attr("code","more");
                            }
                        });
                        
                    });

                }

            });
        },

        initApplyFilterButton: function () {
            var self = this,
                buttonSubmit = this.options.buttonSubmit,
                seoUrlEnable = buttonSubmit.seoUrlEnable,
                urlSuffix = buttonSubmit.urlSuffix;

            if(seoUrlEnable) {
                this.baseSeoFilterParams = this.getSeoUrlParams();
                this.seoFilterParams = this.getSeoUrlParams();
            }

            this.baseFilterParams = this.getUrlParams();
            this.filterParams = this.getUrlParams();


            self.element.find(this.options.buttonSubmitElement).click(function () {
                var baseUrl = buttonSubmit.baseUrl,
                    params = {};

                if(seoUrlEnable && self.seoFilterParams.length){
                    baseUrl = baseUrl.replace(new RegExp(urlSuffix + '$'), '');
                    baseUrl += '/' + self.seoFilterParams.join('-') + urlSuffix;
                }

                for (var elm in self.filterParams) {
                    if (self.filterParams.hasOwnProperty(elm)) {
                        params[elm] = self.filterParams[elm].join('_');
                    }
                }
                params = $.param(params);

                self.ajaxSubmit(baseUrl + (params.length ? '?' + params : ''), true);
            });
        },

        getUrlParams: function (url) {
            var params = {},
                queryString = (typeof url !== 'undefined') ? url.split('?')[1] : window.location.search.slice(1);
            if (queryString) {
                queryString = queryString.split('#')[0];
                queryString = queryString.split('&');

                for (var key in queryString) {
                    if (!queryString.hasOwnProperty(key)) {
                        continue;
                    }

                    var string = queryString[key].split('=');
                    if (string.length === 2) {
                        params[string[0]] = decodeURIComponent(string[1]).split('_');
                    }
                }
            }

            return params;
        },

        

        getSeoUrlParams: function(url){
            var urlSuffix = this.options.buttonSubmit.urlSuffix,
                baseUrl = this.options.buttonSubmit.baseUrl,
                currentUrl = (typeof url !== 'undefined') ? url.split('?')[0] : window.location.origin + window.location.pathname;

            if(baseUrl.length === currentUrl.length){
                return [];
            }

            var paramUrl = currentUrl.replace(new RegExp(urlSuffix + '$'), '').split('/').pop();

            return paramUrl.split('-');
        },

        initProductListUrl: function (useAjax) {
            var isProcessToolbar = false;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                if (isProcessToolbar) {
                    return;
                }
                isProcessToolbar = true;

                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue === defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);
                var submitUrl = baseUrl + (paramData.length ? '?' + paramData : '');

                if (useAjax == 0) {
                    window.location = submitUrl;
                    return;
                }
                submitFilterAction(baseUrl + (paramData.length ? '?' + paramData : ''));
            }
        },

        initButtonParams: function (submitUrl) {
            var params = this.getUrlParams(submitUrl),
                multipleAttrs = this.options.multipleAttrs;

            /**
             * If customer remove selected attribute (default selected only 1 options), param in url will be undefined
             */
            for (var fkey in this.baseFilterParams) {
                if (this.baseFilterParams.hasOwnProperty(fkey) && !params.hasOwnProperty(fkey)) {
                    var baseParamFilter = _.first(this.baseFilterParams[fkey]),
                        baseCurrentFilter = (this.filterParams.hasOwnProperty(fkey)) ? this.filterParams[fkey] : [];

                    if (baseParamFilter) {
                        var baseParamPosition = baseCurrentFilter.indexOf(baseParamFilter);
                        if (baseParamPosition >= 0) {
                            baseCurrentFilter.splice(baseParamPosition, 1);
                        } else {
                            if (multipleAttrs && multipleAttrs.indexOf(fkey) >= 0) {
                                baseCurrentFilter.push(baseParamFilter);
                            } else {
                                baseCurrentFilter = [baseParamFilter];
                            }
                        }

                        if (baseCurrentFilter.length > 0) {
                            this.filterParams[fkey] = baseCurrentFilter;
                        } else {
                            delete this.filterParams[fkey];
                        }
                    }
                }
            }

            for (var key in params) {
                if (!params.hasOwnProperty(key)) {
                    continue;
                }
                var newFilter = params[key],
                    baseFilter = (this.baseFilterParams.hasOwnProperty(key)) ? this.baseFilterParams[key] : [],
                    currentFilter = (this.filterParams.hasOwnProperty(key)) ? this.filterParams[key] : [],
                    paramFilter = params[key];

                //Filter other attribute
                if (_.isEqual(newFilter, baseFilter)) {
                    continue;
                }

                //Remove base filter
                if (newFilter.length < baseFilter.length) {
                    for (var i = 0, len = baseFilter.length; i < len; i++) {
                        if (newFilter.indexOf(baseFilter[i]) < 0) {
                            paramFilter = baseFilter[i];
                        }
                    }
                }

                //Add/remove new filter
                if (newFilter.length > baseFilter.length) {
                    for (var j = 0, lenj = newFilter.length; j < lenj; j++) {
                        if (baseFilter.indexOf(newFilter[j]) < 0) {
                            paramFilter = newFilter[j];
                        }
                    }
                }
                if (paramFilter) {
                    var paramPosition = currentFilter.indexOf(paramFilter);
                    if (paramPosition >= 0) {
                        currentFilter.splice(paramPosition, 1);
                    } else {
                        if (multipleAttrs && multipleAttrs.indexOf(key) >= 0) {
                            currentFilter.push(paramFilter);
                        } else {
                            currentFilter = [paramFilter];
                        }
                    }
                }

                if (currentFilter.length > 0) {
                    var filterLen = currentFilter.length;
                    if (this.options.slider.hasOwnProperty(key)) {
                        this.filterParams[key] = [currentFilter[filterLen - 1]];
                    } else {
                        this.filterParams[key] = currentFilter;
                    }
                } else {
                    delete this.filterParams[key];
                }
            }

            return this;
        },


        selectSwatchOption: function (el) {
            
            var multipleAttrs = this.options.multipleAttrs,
                swatchElms = el.parents('.swatch-attribute'),
                attCode = swatchElms.attr('attribute-code');

            if (multipleAttrs && multipleAttrs.indexOf(attCode) === -1) {
                swatchElms.find('.swatch-option.selected').each(function (index) {
                    $(this).removeClass('selected');
                });
            }

            var childEl = el.find('.swatch-option');
            if (childEl.hasClass('selected')) {
                childEl.removeClass('selected');
            } else {
                childEl.addClass('selected');
            }
        },

        initSlider: function () {

            var self = this,
                slider = this.options.slider;

            for (var code in slider) {
                if (slider.hasOwnProperty(code)) {
                    if (!slider[code]) return false;
                    var sliderConfig = slider[code],
                        sliderElement = self.element.find(this.options.sliderElementPrefix + code),
                        priceFormat = sliderConfig.hasOwnProperty('priceFormat') ? JSON.parse(sliderConfig.priceFormat) : null;

                    if (sliderElement.length) {
                        sliderElement.slider({
                            range: true,
                            min: sliderConfig.minValue,
                            max: sliderConfig.maxValue,
                            values: [sliderConfig.selectedFrom, sliderConfig.selectedTo],
                            slide: function (event, ui) {
                                self.displaySliderText(code, ui.values[0], ui.values[1], priceFormat);
                            },
                            change: function (event, ui) {
                                self.ajaxSubmit(self.getSliderUrl(sliderConfig.ajaxUrl, ui.values[0], ui.values[1]));
                            }
                        });
                    }
                    self.displaySliderText(code, sliderConfig.selectedFrom, sliderConfig.selectedTo, priceFormat);
                }
            }

            //change range input
            var fromInput = this.element.find('input.bss_slider_input_from'),
                toInput = this.element.find('input.bss_slider_input_to');
            if (fromInput && toInput) {
                fromInput.each(function () {
                    var code = self.getSliderCode($(this).attr('id')),
                        sliderMax = self.element.find(self.options.sliderToElementPrefix + code).val(),
                        sliderMin = slider[code].minValue,
                        sliderElement = self.element.find(self.options.sliderElementPrefix + code);

                    $(this).change(function () {
                        var value = parseFloat($(this).val());

                        value = (value > sliderMax) ? sliderMax : ((value < sliderMin) ? sliderMin : value);
                        $(this).val(value);

                        if (sliderElement.length) {
                            sliderElement.slider('values', 0, value);
                        } else {
                            self.ajaxSubmit(self.getSliderUrl(slider[code].ajaxUrl, value, sliderMax));
                        }
                    });
                });

                toInput.each(function () {
                    var code = self.getSliderCode($(this).attr('id')),
                        sliderMax = slider[code].maxValue,
                        sliderMin = self.element.find(self.options.sliderFromElementPrefix + code).val(),
                        sliderElement = self.element.find(self.options.sliderElementPrefix + code);

                    $(this).change(function () {
                        var value = parseFloat($(this).val());

                        value = (value > sliderMax) ? sliderMax : ((value < sliderMin) ? sliderMin : value);
                        $(this).val(value);

                        if (sliderElement.length) {
                            sliderElement.slider('values', 1, value);
                        } else {
                            self.ajaxSubmit(self.getSliderUrl(slider[code].ajaxUrl, sliderMin, value));
                        }
                    });
                });
            }

            var sliderEl = this.element.find('.bss_slider_element');
            if (sliderEl.length) {
                var sliderConfig;
                sliderEl.slider({
                    slide: function (event, ui) {
                        var code = self.getSliderCode($(this).attr('id'));
                        sliderConfig = slider[code];
                        var priceFormat = sliderConfig.hasOwnProperty('priceFormat') ? JSON.parse(sliderConfig.priceFormat) : null;
                        var sliderRange = $(self.options.sliderRangeElementPrefix + code);
                        if (sliderRange.length) {
                            self.element.find(self.options.sliderFromElementPrefix + code).val(ui.values[0]);
                            self.element.find(self.options.sliderToElementPrefix + code).val(ui.values[1]);
                        }
                        self.displaySliderText(code, ui.values[0], ui.values[1], priceFormat);
                    },
                    change: function (event, ui) {
                        var code = self.getSliderCode($(this).attr('id'));

                        self.ajaxSubmit(self.getSliderUrl(slider[code].ajaxUrl, ui.values[0], ui.values[1]));
                    }
                });
            }
        },
        
        displaySliderText: function (code, from, to, format) {
            var textElement = this.element.find(this.options.sliderTextElementPrefix + code);
            if (textElement.length) {
                if (format !== null) {
                    from = this.formatPrice(from, format);
                    to = this.formatPrice(to, format);
                }

                textElement.html(from + ' - ' + to);
            }
            var rangeElement = this.element.find(this.options.sliderRangeElementPrefix + code);
            if (rangeElement.length) {
                this.element.find(this.options.sliderFromElementPrefix + code).val(from);
                this.element.find(this.options.sliderToElementPrefix + code).val(to);
            }
        },

        getSliderUrl: function (url, from, to) {
            return url.replace('from-to', from + '-' + to);
        },

        getSliderCode: function (id) {
            return id.replace('bss_slider_from_', '').replace('bss_slider_to_', '').replace('bss_slider_', '');
        },

        formatPrice: function (value, format) {
            return ultil.formatPrice(value, format);
        },

        ajaxSubmit: function (submitUrl, btnClicked) {
            
            if (this.options.buttonSubmit.enable && (typeof btnClicked === 'undefined')) {
                this.initButtonParams(submitUrl);
                return;
            }

            this.element.find(this.options.mobileShopbyElement).trigger('click');

            if (this.options.useAjax == 0) {
                window.location = submitUrl;
                return;
            }

            return submitFilterAction(submitUrl);
        },

        checkUrl: function (url) {
            var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

            return regex.test(url) ? url : null;
        }
    });

    return $.bss.layer;
});
