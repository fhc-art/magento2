/**
 * Mageants Store Pickup Magento2 Extension 
 */ 
/*global define,alert*/

var objAddress;

define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/cart/totals-processor/default',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/get-totals'
    ],
    function (
        $,
        ko,
        quote,
        totalsDefaultProvider,
        resourceUrlManager,
        storage,
        rateRegistry,
        paymentService,
        methodConverter,
        errorProcessor,
        fullScreenLoader,
        selectBillingAddressAction,
        getTotalsAction
    ) {
        'use strict';
        var stores = window.checkoutConfig.shipping.store_pickup.pickupaddress;
        return {
            saveShippingInformation: function () {
                var payload;
                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }
                var extensionAttributes = "";
                var extensionAttributes = "";
                if (jQuery.cookie("pickupAddress") && quote.shippingMethod().carrier_code == 'storepickup') 
                {
                    objAddress = jQuery.parseJSON(jQuery.cookie("pickupAddress"));
                }
                else
                {
                    objAddress = quote.shippingAddress();
                }
                if(quote.shippingMethod().carrier_code == 'storepickup')
                {
                    if (jQuery.cookie("pickupAddress") == null)
                    {
                        jQuery.cookie("pickupAddress",  JSON.stringify(quote.shippingAddress()), { path: '/' });
                    }
                    if (jQuery.cookie("pickupAddress")) 
                    {
                        objAddress = jQuery.parseJSON(jQuery.cookie("pickupAddress"));
                    }
                    var str = $('[name="pickup_store"]').val();
                    var strDate = $('[name="pickup_date"]').val();
                    if(str == '' || strDate == ''){
                        $(".require-label").html('Pickup Store & Dates are required');
                        return false;
                    }
                    storeName = '';
                    streetAddress = '';
                    if(stores[str]){
                        var storeName = stores[str].name.split(' ');
                        var streetAddress = [stores[str].address];
                        quote.shippingAddress().street = streetAddress;
                    }
                    var firstname = '';
                    var lastname = '';
                    if (storeName.length == 1)
                    {
                        quote.shippingAddress().firstname = 'Store';
                        quote.shippingAddress().lastname = storeName[0];
                    }
                    else if (storeName.length > 2)
                    {
                        quote.shippingAddress().firstname = storeName[0];
                        for (var i = 1; i < storeName.length; i++)
                        {
                            quote.shippingAddress().lastname = quote.shippingAddress().lastname+" "+storeName[i];
                        }
                    }
                    else{
                        quote.shippingAddress().firstname = storeName[0];
                        quote.shippingAddress().lastname = storeName[1];
                    }
                    
                    if(stores[str]){
                        quote.shippingAddress().city = stores[str].city;
                        quote.shippingAddress().customerAddressId = null;
                        quote.shippingAddress().customerId= null;

                        quote.shippingAddress().postcode = jQuery.trim(stores[str].postcode);
                        quote.shippingAddress().region = jQuery.trim(stores[str].region);
                        quote.shippingAddress().regionId =  parseInt(stores[str].region_id);
                        quote.shippingAddress().regionCode = stores[str].code;
                        quote.shippingAddress().countryId = jQuery.trim(stores[str].country).toUpperCase();
                        quote.shippingAddress().telephone = jQuery.trim(stores[str].phone);
                    }
                    jQuery.cookie("pickupAddress",  JSON.stringify(quote.shippingAddress()), { path: '/' });
                    var extensionAttributes = {
                        pickup_date: $('[name="pickup_date"]').val(),
                        pickup_store_val: $('[name="pickup_store"]').val(),
                        pickup_store: $('[name="pickup_store"] option:selected').text()
                    }
                }
                else{
                    if(objAddress != ""){
                        quote.shippingAddress().firstname = objAddress.firstname;
                        quote.shippingAddress().lastname = objAddress.lastname;
                        quote.shippingAddress().street = objAddress.street;
                        quote.shippingAddress().city = objAddress.city;
                        quote.shippingAddress().telephone = objAddress.telephone;
                        quote.shippingAddress().postcode = objAddress.postcode;
                        quote.shippingAddress().region = objAddress.region;
                        quote.shippingAddress().regionId = objAddress.regionId;
                        quote.shippingAddress().countryId = objAddress.countryId;
                    }
                }
            
                if (extensionAttributes == "") {
                    payload = {
                        addressInformation: {
                            shipping_address: quote.shippingAddress(),
                            billing_address: quote.billingAddress(),
                            shipping_method_code: quote.shippingMethod().method_code,
                            shipping_carrier_code: quote.shippingMethod().carrier_code
                        }
                    };
                }
                else{
                    payload = {
                        addressInformation: {
                            shipping_address: quote.shippingAddress(),
                            billing_address: quote.billingAddress(),
                            shipping_method_code: quote.shippingMethod().method_code,
                            shipping_carrier_code: quote.shippingMethod().carrier_code,
                            extension_attributes: extensionAttributes
                        }
                    };
                }

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );


                }
            };
    }
);
