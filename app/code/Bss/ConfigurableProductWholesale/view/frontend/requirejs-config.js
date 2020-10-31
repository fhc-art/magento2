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
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

var config = {
    map: {
        '*': {
            'bss/configurableproductwholesale': 'Bss_ConfigurableProductWholesale/js/configurableproductwholesale',
            'mage/SwatchRenderer': 'Magento_Swatches/js/swatch-renderer',
            'wholesale-renderer': 'Bss_ConfigurableProductWholesale/js/swatch-renderer',
            'bss/configurableproductwholesale_ajax': 'Bss_ConfigurableProductWholesale/js/configurableproductwholesale_ajax'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/price-box': {
                'Bss_ConfigurableProductWholesale/js/price-box': true
            },
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Bss_ConfigurableProductWholesale/js/catalog-add-to-cart': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Bss_ConfigurableProductWholesale/js/swatch-renderer-fix': true
            }
        }
    }
};
