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
 * @category   BSS
 * @package    Bss_CustomShippingMethod
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    "jquery",
    "mage/adminhtml/form"
], function ($) {
    'use strict';
    return function () {
        var val = $('#bss_applicable_countries').children("option:selected").val();
        if (val== 0) {
            $('#bss_specific_countries').attr("disabled",true);
        }
        $('#bss_applicable_countries').on('change',function () {
            if (this.value == 0) {
                $('#bss_specific_countries').attr("disabled",true);
            } else {
                $('#bss_specific_countries').attr("disabled",false);
            }
        });
    }
});
