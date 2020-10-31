<?php
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
 * @category  BSS
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class CheckNumber extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $html .= '<div class="maxProduct"></div>';
        $html .= '
        <script type="text/javascript">
            require(["jquery", "jquery/ui","Magento_Ui/js/modal/modal"], function($,modal){
                var checkMaxProduct = 2;
                $("#layered_navigation_general_less_more").keyup(function (e) {
                    var maxProduct = $(this).val();
                    if(maxProduct >= 0) {
                        checkMaxProduct = 1;
                        $(\'.maxProduct .error\').remove();
                    }
                    else {
                        checkMaxProduct = 0;
                        $(\'.maxProduct\').html("" +
                         "<div class=\'error\'>Please fill in the correct number format</div>");
                    }
                }); 

                $("#config-edit-form").bind(\'submit\', function (e) {
                    if(checkMaxProduct == 0){
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        alert("Please fill in the correct number format");
                        return false;
                    }
                });
            });
        </script>
        <style type="text/css">
            .maxProduct{
                padding: 0px;
            }
            .maxProduct .error{
                padding: 5px;
                background-color: #FFD89E;
                color: red;
                margin: 5px 0 0 0;
            }
        </style>';
        return $html;
    }
}
