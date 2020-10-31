define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'paybycredit',
                component: 'Ced_CreditLimit/js/view/payment/creditlimit/creditpayment'
            }
        );
        return Component.extend({});
    }
);