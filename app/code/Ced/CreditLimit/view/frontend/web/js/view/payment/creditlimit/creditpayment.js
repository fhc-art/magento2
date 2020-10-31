define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/translate',
        'ko'
    ],
    function ($,Component,ko) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Ced_CreditLimit/payment/creditlimit'
            },
            getRemainingAmount: function() {
               var remainingAmount =  JSON.parse(remainingCreditLimit);
              return remainingAmount;

            }
        });
    }
);