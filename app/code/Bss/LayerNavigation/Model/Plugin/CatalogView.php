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
namespace Bss\LayerNavigation\Model\Plugin;

class CatalogView
{
    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView $subject
     * @param $result
     * @param $request
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsApplicable(
        \Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView $subject,
        $result,
        $request
    ) {
        return true;
    }
}
