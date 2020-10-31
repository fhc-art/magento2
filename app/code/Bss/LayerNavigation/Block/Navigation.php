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
namespace Bss\LayerNavigation\Block;

use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Context;

class Navigation extends \Magento\LayeredNavigation\Block\Navigation
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
     * @var
     */
    protected $filterModel;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $encoderInterface;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Navigation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList $filterList
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag
     * @param \Bss\LayerNavigation\Helper\Data $moduleHelper
     * @param \Magento\Framework\Json\EncoderInterface $encoderInterface
     * @param \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        \Bss\LayerNavigation\Helper\Data $moduleHelper,
        \Magento\Framework\Json\EncoderInterface $encoderInterface,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerResolver,
            $filterList,
            $visibilityFlag,
            $data
        );
        $this->moduleFilter = $moduleFilter;
        $this->encoderInterface = $encoderInterface;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @return \Bss\LayerNavigation\Helper\Data
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * @param $filters
     * @return string
     */
    public function getLayerConfiguration($filters)
    {
        $filterParams = $this->getRequest()->getParams();

        $config = $this->dataObjectFactory->create();
        $config->setData([
            'active' => array_keys($filterParams),
            'params' => $filterParams
        ]);

        $this->getFilterModel()->getLayerConfiguration($filters, $config);

        return $this->encoderInterface->encode($config->getData());
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
