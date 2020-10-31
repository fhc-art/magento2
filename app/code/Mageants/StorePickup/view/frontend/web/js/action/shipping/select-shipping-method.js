/**
 * Mageants Store Pickup Magento2 Extension 
 */ 
/*global define,alert*/
define(
    [
        'Magento_Checkout/js/model/quote'
    ],
    function (quote) {
        "use strict";
        return function (shippingMethod) {
            quote.shippingMethod(shippingMethod)
            
            if(shippingMethod != null)
            {
                //alert(JSON.stringify(shippingMethod));
                var code = shippingMethod.carrier_code;
                   jQuery.cookie("selected-val", code, {path: '/'});
                if(code == 'storepickup'){
                    jQuery('#store-pickup-additional-block').show();
                }
                else{
                    jQuery('#store-pickup-additional-block').hide();
                }
            } 
            jQuery("#pickup_store").change(function(){
                jQuery( "#pickup_date" ).focus();
            });          
        }
    }
);
