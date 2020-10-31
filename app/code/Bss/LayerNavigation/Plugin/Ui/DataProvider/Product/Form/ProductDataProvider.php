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
namespace Bss\LayerNavigation\Plugin\Ui\DataProvider\Product\Form;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider as CoreProductDataProvider;

class ProductDataProvider
{
    /**
     * @param CoreProductDataProvider $subject
     * @param array $result
     * @return array $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMeta(CoreProductDataProvider $subject, $result)
    {
        unset($result['product-details']['children']['container_rating']['children']['rating']);
        return $result;
    }
}
