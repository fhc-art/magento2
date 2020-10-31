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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_OrderAmount
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-save-processor',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Ui/js/model/messageList'
], function ($, quote, shippingSaveProcessor, paymentdefault, messageList) {
    'use strict';

    return function () {
        $.ajax({
                type: 'post',
                url: window.checkoutConfig.baseUrl+'orderamount/',
                dataType: 'json',
                success: function (data) {
                    try {
                        if (data.success) {
                           $('.action.checkout').removeClass('disabled').removeAttr('disabled');
                        } else {
                            $('.action.checkout').addClass('disabled').attr('disabled','disabled');
                            messageList.addErrorMessage(data);
                        }
                    } catch(error) {
                        console.log(error);
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        return shippingSaveProcessor.saveShippingInformation(quote.shippingAddress().getType());
    };
});
