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
namespace Bss\LayerNavigation\Plugin\Model\Layer\Category;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList as CoreFilterableAttributeList;

class FilterableAttributeList
{
    /**
     * FilterableAttributeList constructor.
     * @param \Bss\LayerNavigation\Helper\Data $helper
     */
    public function __construct(
        \Bss\LayerNavigation\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param CoreFilterableAttributeList $subject
     * @param array|Collection $result
     * @return array|Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(CoreFilterableAttributeList $subject, $result)
    {
        if (!$this->helper->isEnabled() || !$this->helper->isRating()) {
            $result->clear();
            $result->addFieldToFilter('attribute_code', ['nin' => ['rating']])->load();
        }
        return $result;
    }
}
