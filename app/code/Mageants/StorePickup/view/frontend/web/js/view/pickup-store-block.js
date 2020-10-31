/**
 * Mageants Store Pickup Magento2 Extension 
 */  
define(
    [
    'jquery',
    'ko',
    'uiComponent'
    ], function ($, ko, Component) {
    'use strict';

    return Component.extend(
        {
        defaults: {
        template: 'Mageants_StorePickup/pickup-store-block'
        },
        initialize: function () {
        this._super();
        var enableextension = window.checkoutConfig.shipping.store_pickup.enableextension;
        var disabled = window.checkoutConfig.shipping.store_pickup.disabled;
        var noday = window.checkoutConfig.shipping.store_pickup.noday;
        var hourMin = parseInt(window.checkoutConfig.shipping.store_pickup.hourMin);
        var hourMax = parseInt(window.checkoutConfig.shipping.store_pickup.hourMax);
        var format = window.checkoutConfig.shipping.store_pickup.format;
        var disableDays = window.checkoutConfig.shipping.store_pickup.disableDays;
        var holidays = window.checkoutConfig.shipping.store_pickup.holidays;
        this.stores = window.checkoutConfig.shipping.store_pickup.stores;
        var obj = JSON.parse(holidays);
        var holidayList = [];
        $.each(obj, function( index, value ) {
            holidayList.push(value.Date);
        });
        if($.cookie("selected-val") && $.cookie("selected-val") != null && $.cookie("selected-val") == 'storepickup' && enableextension == 1)
            {
                this.enabled = "display:block";
            }
            else
            {
                this.enabled = "display:none";
            }
            
            if(!format) {
                format = 'yy-mm-dd';
            }
            var disabledDay = disabled.split(",").map(
                function(item) {
                return parseInt(item, 10);
                }
            );
        ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    //initialize datetimepicker
                    if(noday && $.isEmptyObject(holidayList)) {
                        var options = {
                            minDate: disableDays,
                            dateFormat:format,
                            hourMin: hourMin,
                            hourMax: hourMax
                        };
                    } else {
                        var options = {
                            minDate: disableDays,
                            dateFormat:format,
                            hourMin: hourMin,
                            hourMax: hourMax,
                            beforeShowDay: function(date) {
                                var day = date.getDay();
                                if(disabledDay.indexOf(day) > -1 || holidayList.indexOf(date.toLocaleDateString()) > -1) {
                                    return [false];
                                } else {
                                    return [true];
                                }
                            }
                        };
                    }
                    $el.datetimepicker(options);

                    var writable = valueAccessor();
                    if (!ko.isObservable(writable)) {
                        var propWriters = allBindingsAccessor()._ko_property_writers;
                        if (propWriters && propWriters.datetimepicker) {
                            writable = propWriters.datetimepicker;
                        } else {
                            return;
                        }
                    }
                    writable($(element).datetimepicker("getDate"));
                },
                update: function (element, valueAccessor) {
                    var widget = $(element).data("DateTimePicker");
                    //when the view model is updated, update the widget
                    if (widget) {
                        var date = ko.utils.unwrapObservable(valueAccessor());
                        widget.date(date);
                    }
                }
            };
        return this;
        }
        }
    );
    }
);
