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
namespace Bss\LayerNavigation\Model\ResourceModel;

use Bss\LayerNavigation\Helper\Data as LayerHelper;

class Item
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $htmlPagerBlock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var LayerHelper
     */
    protected $moduleHelper;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $moduleFilter;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $filterModel;

    /**
     * Item constructor.
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param \Magento\Framework\App\RequestInterface $request
     * @param LayerHelper $moduleHelper
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        \Magento\Framework\App\RequestInterface $request,
        LayerHelper $moduleHelper
    ) {
        $this->moduleFilter = $moduleFilter;
        $this->url = $url;
        $this->htmlPagerBlock = $htmlPagerBlock;
        $this->request = $request;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param $proceed
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
        $value = [];
        $filter = $item->getFilter();
        $filterModel = $this->getFilterModel();
        if ($filterModel->isSliderTypes($filter) || $filter->getData('range_mode')) {
            $value = ["from-to"];
        } elseif ($filterModel->isMultiple($filter)) {
            $requestVar = $filter->getRequestVar();
            if ($requestValue = $this->request->getParam($requestVar)) {
                $value = explode('_', $requestValue);
            }
            if (!in_array($item->getValue(), $value)) {
                $value[] = $item->getValue();
            }
        }

        if (!empty($value)) {
            $query = [
                $filter->getRequestVar() => implode('_', $value),
                // exclude current page from urls
                $this->htmlPagerBlock->getPageVarName() => null,
            ];

            return $this->url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        }
        return $proceed();
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $item
     * @param $proceed
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
        $value = [];
        $filter = $item->getFilter();
        $filterModel = $this->getFilterModel();
        if ($filterModel->isMultiple($filter)) {
            $value = $filterModel->getFilterValue($filter);
            if (in_array($item->getValue(), $value)) {
                $value = array_diff($value, [$item->getValue()]);
            }
        }

        $params['_query'] = [
            $filter->getRequestVar() => (count($value) &&
                ($filter->getRequestVar() != 'price') )? implode('_', $value) : $filter->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_escape'] = true;

        return $this->url->getUrl('*/*/*', $params);
    }

    /**
     * @return mixed
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->moduleFilter;
        }
        return $this->filterModel;
    }
}
