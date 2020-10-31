/**
 * Mageants Store Pickup Magento2 Extension 
 */ 
var config = {
    "map": {
        "*": {
            'Magento_Checkout/js/model/shipping-save-processor/default': 'Mageants_StorePickup/js/model/shipping-save-processor/default',
            'Magento_Checkout/js/action/select-shipping-method': 'Mageants_StorePickup/js/action/shipping/select-shipping-method',
            'Magento_Checkout/js/view/shipping': 'Mageants_StorePickup/js/view/shipping',
            'Magento_Checkout/js/view/shipping-information': 'Mageants_StorePickup/js/view/shipping-information'
        }
    }
};
