/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'jquery/jquery.cookie',
    'domReady!',
], function (ko,$, Component, customerData, quote, stepNavigator, sidebarModel) {
    'use strict';
    var countryData = customerData.get('directory-data');
    return Component.extend({
        defaults: {
            template: 'Mageants_StorePickup/shipping-information'
        },

        /**
         * @return {String}
         */
        getCountryName: function () {
            var obj = JSON.parse($.cookie('pickupAddress'));
            this.tel= ko.observable(obj.telephone);
            return countryData()[obj.countryId] != undefined ? countryData()[obj.countryId].name : ''; 
        },

        /**
         * @return {String}
         */
        telephone: function () {
            var obj = JSON.parse($.cookie('pickupAddress'));
            return obj.telephone; 
        },

        /**
         * @return {Boolean}
         */
        isVisible: function () {
            return !quote.isVirtual() && stepNavigator.isProcessed('shipping');
        },

        /**
         * @return {String}
         */
        getShippingMethodTitle: function () {
            var shippingMethod = quote.shippingMethod();

            return shippingMethod ? shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'] : '';
        },

        /**
         * Back step.
         */
        back: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping');
        },      

        /**
         * Back to shipping method.
         */
        backToShippingMethod: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping', 'opc-shipping_method');
        },
         /*
          * @return {String}
         */
        getPickup: function () {
            if(quote.shippingMethod().carrier_code == 'storepickup' && parseInt($.cookie('pickupStoreVal')) === 1)
            {
                if($.cookie('pickupAddress')){
                    var obj = JSON.parse($.cookie('pickupAddress'));
                    var str = '<div>'+obj.firstname+ " " + obj.lastname + "</div> <div>"+obj.street[0]+"</div><div>"+obj.city+" "+obj.region+" <a href='post"+obj.postcode+"'>"+obj.postcode+"<a/></div>";
                    return str;
                }
            }
            return false;
        }
    });
});
