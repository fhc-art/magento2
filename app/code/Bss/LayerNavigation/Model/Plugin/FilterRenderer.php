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

class FilterRenderer
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $manager;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $moduleFilter;

    /**
     * {@inheritdoc}
     */
    protected $filterModel;

    /**
     * FilterRenderer constructor.
     * @param \Magento\Framework\Event\Manager $manager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter
     * @param \Bss\LayerNavigation\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Event\Manager $manager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        \Bss\LayerNavigation\Helper\Data $helper
    ) {
        $this->moduleFilter = $moduleFilter;
        $this->layout = $layout;
        $this->helper = $helper;
        $this->manager = $manager;
    }

    /**
     * @param \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {
        if ($this->helper->isEnabled()) {
            $displayTypes = $this->helper->getDisplayTypes();
            $filterType = $this->getFilterModel()->getFilterType($filter);

            if (isset($displayTypes[$filterType]) && isset($displayTypes[$filterType]['class'])) {
                $this->manager->dispatch('custom_display_filter', ['filter' => $filter]);
                if ($filter->getCustomDisplayFilter()) {
                    return $filter->getCustomDisplayFilter();
                }

                return $this->layout
                    ->createBlock($displayTypes[$filterType]['class'])
                    ->setFilter($filter)
                    ->toHtml();
            }
        }
        return $proceed($filter);
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
