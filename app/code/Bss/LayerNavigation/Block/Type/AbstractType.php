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
namespace Bss\LayerNavigation\Block\Type;

use Magento\Framework\View\Element\Template;
use Bss\LayerNavigation\Helper\Data as LayerHelper;

class AbstractType extends Template
{
    /**
     * @var string
     */
    protected $_template = '';

    /**
     * @var
     */
    protected $filter;

    /**
     * @var LayerHelper
     */
    protected $helper;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $moduleFilter;

    /**
     * {@inheritdoc}
     */
    protected $filterModel;

    /**
     * AbstractType constructor.
     * @param Template\Context $context
     * @param LayerHelper $helper
     * @param \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        LayerHelper $helper,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        array $data = []
    ) {
        $this->moduleFilter = $moduleFilter;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return LayerHelper
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->filter->getItems();
    }

    /**
     * @return mixed
     */
    public function isMultipleMode()
    {
        $filter = $this->getFilter();

        return $this->getFilterModel()->isMultiple($filter);
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return $this
     */
    public function setFilter(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        $this->filter = $filter;

        return $this;
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

    /**
     * @return mixed
     */
    public function isSearchEnable()
    {
        return $this->getFilterModel()->isSearchEnable();
    }

    /**
     * @return mixed
     */
    public function getAttributeCode()
    {
        return $this->filter->getRequestVar();
    }

    /**
     * @return string
     */
    public function getBlankUrl()
    {
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = [$this->filter->getRequestVar() => $this->filter->getResetValue()];
        $params['_escape']      = true;

        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }
}
