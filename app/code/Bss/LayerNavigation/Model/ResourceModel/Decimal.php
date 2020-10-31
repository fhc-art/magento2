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

use Magento\CatalogSearch\Model\Layer\Filter\Decimal as DecimalCore;
use Bss\LayerNavigation\Helper\Data as LayerHelper;

class Decimal extends DecimalCore
{
    /**
     * @var LayerHelper
     */
    protected $moduleHelper;

    /**
     * @var null
     */
    protected $filterVal = null;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory
     */
    protected $dataProviderFactory;

    /**
     * @var
     */
    protected $productCollection;

    protected $minValue = 0;
    protected $maxValue = 0;

    /**
     * Decimal constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
     * @param LayerHelper $moduleHelper
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        LayerHelper $moduleHelper,
        \Magento\Tax\Helper\Data $taxHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $filterDecimalFactory,
            $priceCurrency,
            $data
        );
        $this->priceCurrency = $priceCurrency;
        $this->moduleHelper = $moduleHelper;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->taxHelper  = $taxHelper;
    }

    /**
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase
     */
    protected function renderRangeLabel($fromPrice, $toPrice)
    {
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if ($toPrice === '' || $toPrice == 0) {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice &&
            $this->dataProviderFactory->create(
                ['layer' => $this->getLayer()]
            )->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }

    /**
     * @return array
     */
    public function getSliderConfig()
    {
        $min = $this->minValue;
        $max = $this->maxValue;

        list($from, $to) = $this->filterVal ?: [$min, $max];
        $from = ($from < $min) ? $min : (($from > $max) ? $max : $from);
        $to = ($to > $max) ? $max : (($to < $from) ? $from : $to);

        $item = $this->getItems()[0];

        return [
            "selectedFrom" => $from,
            "selectedTo" => $to,
            "minValue" => $min,
            "maxValue" => $max,
            "priceFormat" => $this->taxHelper->getPriceFormat(),
            "ajaxUrl" => $item->getUrl()
        ];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getItemsData()
    {
        if (!$this->moduleHelper->isEnabled()) {
            return parent::_getItemsData();
        }

        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $productCollection = $this->getLayer()->getProductCollection();

        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());

        $data = [];
        $countNumber = 0;
        
        if (count($facets) > 1) {
            foreach ($facets as $key => $aggregation) {
                if ($countNumber == 0) {
                    $this->minValue = (int)$aggregation['value'];
                }
                $this->maxValue = (int)$aggregation['value'];
                $countNumber++;
                $count = $aggregation['count'];
                if (strpos($key, '_') === false) {
                    continue;
                }

                list($from, $to) = explode('_', $key);
                if ($from == '*') {
                    $from = '';
                }
                if ($to == '*') {
                    $to = '';
                }
                $label = $this->renderRangeLabel(
                    empty($from) ? 0 : $from,
                    empty($to) ? 0 : $to
                );
                $value = $from . '-' . $to;

                $data[] = [
                    'label' => $label,
                    'value' => $value,
                    'count' => $count,
                    'from' => $from,
                    'to' => $to
                ];
            }
        }

        return $data;
    }
}
