/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'jquery/jquery.cookie',
    'domReady!',
    'Magento_Checkout/js/model/quote',
], function (Component, customerData, $,ck, dr,quote) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Mageants_StorePickup/shipping-information/address-renderer/default'
        },

        /**
         * @param {*} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /**
         * @param {*} countryId
         * @return {String}
         */
        getPickup: function () {
            if(quote.shippingMethod().carrier_code == 'storepickup' && parseInt($.cookie('pickupStoreVal')) === 1)
            {
                if($.cookie('pickupAddress')){
                    var obj = JSON.parse($.cookie('pickupAddress'));
                    var str = '<div>'+obj.firstname+ " " + obj.lastname + "</div> <div>"+obj.street[0]+"</div><div>"+obj.city+" "+obj.region+" <a href='post"+obj.postcode+"'>"+obj.postcode+"<a/></div><div databind=' ko text: getCountryName("+obj.countryId+")'></div><div><a href='tel:"+obj.telephone+"'>"+obj.telephone+"</a></div>";
                    return str;
                }
            }
            return false;
        }
    });
});
