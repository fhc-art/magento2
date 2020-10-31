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
namespace Bss\LayerNavigation\Plugin;

use Magento\Swatches\Model\Plugin\FilterRenderer as CoreRenderer;

class FilterRenderer extends CoreRenderer
{
    /**
     * @var string
     */
    protected $block = Bss\LayerNavigation\Block\LayeredNavigation\RenderLayered::class;

    /**
     * @param FilterInterface $filter
     * @return mixed
     */
    public function render(FilterInterface $filter)
    {
        $this->assign('filterItems', $filter->getItems());
        $this->assign('filter', $filter);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }

    /**
     * @param $filter
     * @return array
     */
    public function getPriceRange($filter)
    {
        $Filterprice = ['min' => 0 , 'max' => 0];
        if ($filter instanceof Magento\CatalogSearch\Model\Layer\Filter\Price) {
            $priceArr = $filter->getResource()->loadPrices(10000000000);
            $Filterprice['min'] = reset($priceArr);
            $Filterprice['max'] = end($priceArr);
        }
        return $Filterprice;
    }

    /**
     * @param $filter
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFilterUrl($filter)
    {
        $query = ['price'=> ''];
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}
