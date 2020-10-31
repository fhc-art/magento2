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
namespace Bss\LayerNavigation\Plugin\Block;

class RenderLayered
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
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $moduleHelper;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $filter;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $moduleFilter;

    /**
     * {@inheritdoc}
     */
    protected $filterModel;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        \Bss\LayerNavigation\Helper\Data $moduleHelper
    ) {
        $this->moduleFilter = $moduleFilter;
        $this->url = $url;
        $this->htmlPagerBlock = $htmlPagerBlock;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetSwatchFilter(
        \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject,
        \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
    ) {
        $this->filter = $filter;

        return [$filter];
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param $proceed
     * @param $attributeCode
     * @param $optionId
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuildUrl(
        \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject,
        $proceed,
        $attributeCode,
        $optionId
    ) {
        if (!$this->moduleHelper->isEnabled()) {
            return $proceed($attributeCode, $optionId);
        }

        $attHelper = $this->getFilterModel();

        if ($attHelper->isMultiple($this->filter)) {
            $value = $attHelper->getFilterValue($this->filter);

            if (!in_array($optionId, $value)) {
                $value[] = $optionId;
            } else {
                $key = array_search($optionId, $value);
                if ($key !== false) {
                    unset($value[$key]);
                }
            }
        } else {
            $value = [$optionId];
        }

        $query = !empty($value) ? [$attributeCode => implode('_', $value)] : [$attributeCode => null];

        return $this->url->getUrl(
            '*/*/*',
            ['_current' => true, '_use_rewrite' => true, '_escape' => false, '_query' => $query]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->moduleFilter;
        }
        return $this->filterModel;
    }
}
