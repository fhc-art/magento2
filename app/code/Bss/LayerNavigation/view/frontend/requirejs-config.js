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
var config = {
    paths: {
        bssLayer : 'Bss_LayerNavigation/js/ajax_layered',
        bssChosen: 'Bss_LayerNavigation/js/jquery/chosen.jquery.min',
        uiTouchPunch: 'Bss_LayerNavigation/js/jquery/jquery.ui.touch-punch.min'
    },
    shim: {
        bssChosen: ['jquery'],
        uiTouchPunch: ['jquery/ui']
    }
};
