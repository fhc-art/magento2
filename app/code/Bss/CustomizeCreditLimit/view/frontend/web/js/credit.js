define([
    'jquery',
    'mage/mage'
], function($){
    var size_li =  0;
    var x = 10;
    var lastShow = 0;
    $('.bss-hidden').hide();
    $('#show-more').css({"cursor":"pointer","margin": "0 auto","width": "10%","padding": "8px 10px","background-color": "#eeeeee","text-align": "center", "margin-bottom": "1%"});
    $('#show-less').css({"cursor":"pointer","margin": "0 auto","width": "10%","padding": "8px 10px","background-color": "#eeeeee","text-align": "center","margin-bottom": "1%"});
    $('#show-less').hide();
    $("body").on('DOMSubtreeModified', ".credit-paid-order", function() {
        var size = $("#my-paid-orders-table tbody tr").size();
        $('.bss-hidden').hide();
        $('#my-paid-orders-table tbody tr:lt(10)').show();
        $('#my-paid-orders-table tbody tr').not(':lt(10)').hide();
        $('#show-more').css({"cursor":"pointer","margin": "0 auto","width": "10%","padding": "8px 10px","background-color": "#eeeeee","text-align": "center", "margin-bottom": "1%"});
        $('#show-less').css({"cursor":"pointer","margin": "0 auto","width": "10%","padding": "8px 10px","background-color": "#eeeeee","text-align": "center","margin-bottom": "1%"});
        $('#show-less').hide();
    });

    $(document).ready(function () {
        $('body').on('click', '#show-more', function () {
            size_li = $(this).parent().find("#my-paid-orders-table tbody tr").size();
            $(this).parent().find("#my-paid-orders-table tbody tr.bss-hidden").size()

            x= (x+10 <= size_li) ? x+10 : size_li;
            $(this).parent().find('#my-paid-orders-table tbody tr:lt('+x+')').show();
            $(this).parent().find('#show-less').show();
            if (x == size_li) {
                lastShow = size_li%10;
                $(this).hide();
            } else {
                lastShow = 0;
            }

        });
        $('body').on('click', '#show-less', function () {
            $(this).parent().find('#show-more').show();
            size_li = $(this).parent().find("#my-paid-orders-table tbody tr").size();
            if (lastShow > 0 && x !== size_li) {
                x = x - lastShow;
                lastShow = 0;
            }  else {
                x=(x-10 <10) ? 10 : x-10;
            }
            $(this).parent().find('#my-paid-orders-table tbody tr').not(':lt('+x+')').hide();
            if (x <= 10) {
                $(this).hide();
            }
        });
    });
    //setup binds for click
    $('body').on('click','#form_edit button', function () {
        var arr = 0;
        var str = orders= '';
        jQuery('.pay-invoice-credit:checked').each(function () {
            arr += Number(jQuery(this).val());
            if (jQuery(this).parent().parent().find('.invoice-credit-limit').val() !== "") {
                str = str.concat(",");
            }
            str = str.concat(jQuery(this).parent().parent().find('.invoice-credit-limit').val());
            orders = orders.concat(jQuery(this).parent().parent().find('.order-credit-limit').val());
            orders = orders.concat(",");
        });
        if (str.charAt(0) === ",") {
            str = str.substr(1);
        }
        $('#amount-credit-number').val(arr);
        $('#invoice-credit-string').val(str);
        $('#order-credit-string').val(orders);
    });

    $('body').on('click','.pay-invoice-credit', function () {
        var arr = 0;
        var str = orders= '';
        jQuery('.pay-invoice-credit:checked').each(function () {
            arr += Number(jQuery(this).val());
            if (jQuery(this).parent().parent().find('.invoice-credit-limit').val() !== "") {
                str = str.concat(",");
            }
            str = str.concat(jQuery(this).parent().parent().find('.invoice-credit-limit').val());
            orders = orders.concat(jQuery(this).parent().parent().find('.order-credit-limit').val());
            orders = orders.concat(",");
        });
        if (str.charAt(0) === ",") {
            str = str.substr(1);
        }
        $('#amount-credit-number').val(arr);
        $('.credit-label-total-number').text((Math.round(arr * 100) / 100).toFixed(2));
        $('#invoice-credit-string').val(str);
        $('#order-credit-string').val(orders);
    });
    $(document).ready(function () {
        var arr = 0;
        var str = orders= '';
        jQuery('.pay-invoice-credit:checked').each(function () {
            arr += Number(jQuery(this).val());
            if (jQuery(this).parent().parent().find('.invoice-credit-limit').val() !== "") {
                str = str.concat(",");
            }
            str = str.concat(jQuery(this).parent().parent().find('.invoice-credit-limit').val());
            orders = orders.concat(jQuery(this).parent().parent().find('.order-credit-limit').val());
            orders = orders.concat(",");
        });
        if (str.charAt(0) === ",") {
            str = str.substr(1);
        }
        $('#amount-credit-number').val(arr);
        $('.credit-label-total-number').text((Math.round(arr * 100) / 100).toFixed(2));
        $('#invoice-credit-string').val(str);
        $('#order-credit-string').val(orders);
    });
});
