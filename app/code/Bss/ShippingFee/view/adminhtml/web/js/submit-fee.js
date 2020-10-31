/**
 *
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 *  @category  BSS
 *  @package   Bss_ShippingFee
 *  @author    Extension Team
 *  @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 *  @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert, $t) {
 	$.widget('bss.shipping_fee', {
 		options: {
            submiturl: ''
        },

 		 _create: function () {
 		 	var options = this.options;
 		 	$("#add-shipping-fee").click(function(e) {
			    e.preventDefault();
			    var fee = $(this).closest('.bss-shipping-fee').find('.shippingfee-input').val();
			    if (fee < 0) {
			    	alert({
                        title: $t('Error'),
                        content: $t('Please enter a value 0 or greater in shipping fee.'),
                    });
			    } else {

				    $.ajax({
				        type: "POST",
				        url: options.submitUrl,
				        showLoader: true,
				        data: { 
				            fee: fee,
				            orderId: options.orderId,
				            items: $('#invoice_item_container :input').serialize()
				        },
				        success: function(result) {
				            if (result.error) {
			                    alert({
			                        title: $t('Error'),
			                        content: result.message
			                    });
			                } else {
			                    alert({
			                        title: $t('Success'),
			                        content: result.message
			                    });
			                    $('#invoice_item_container').html(result.html);
			                }
				        },
				        error: function(result) {
				            alert(result);
				        }
				    });
				}
			});
 		 }
 	});
 	return $.bss.shipping_fee;
});