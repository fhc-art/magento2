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
namespace Bss\LayerNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $moduleHelper;

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
     * @param Template\Context $context
     * @param \Bss\LayerNavigation\Helper\Data $moduleHelper
     * @param \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\LayerNavigation\Helper\Data $moduleHelper,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        array $data = []
    ) {
        $this->moduleFilter = $moduleFilter;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Bss\LayerNavigation\Helper\Data
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getThisFilter()
    {
        return $this->getFilter();
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
