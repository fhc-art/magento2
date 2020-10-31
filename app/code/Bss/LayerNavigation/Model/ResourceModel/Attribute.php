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

use Magento\CatalogSearch\Model\Layer\Filter\Attribute as CoreAttribute;
use Bss\LayerNavigation\Helper\Data as BssHelper;

/**
 * Class Attribute
 * @package Bss\LayerNavigation\Model\ResourceModel
 */
class Attribute extends CoreAttribute
{
    /**
     * @var BssHelper
     */
    protected $moduleHelper;

    /**
     * @var bool
     */
    protected $isFilter = true;

    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    private $tagFilter;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $moduleFilter;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $filterModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $cloneProductCollection;

    /*
     *
     */
    protected $optionsFacetedData;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\ItemCollectionProvider
     */
    protected $collectionProvider;

    /**
     * Attribute constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter
     * @param BssHelper $moduleHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Bss\LayerNavigation\Model\Layer\ItemCollectionProvider $collectionProvider
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Filter\StripTags $tagFilter,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        BssHelper $moduleHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Bss\LayerNavigation\Model\Layer\ItemCollectionProvider $collectionProvider,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $tagFilter,
            $data
        );
        $this->moduleFilter = $moduleFilter;
        $this->tagFilter = $tagFilter;
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
        $this->collectionProvider = $collectionProvider;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return parent::apply($request);
        }

        $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue)) {
            $this->isFilter = false;
            return $this;
        }

        $productCollection = $this->getLayer()
            ->getProductCollection();

        $attributeValue = explode('_', $attributeValue);
        $attributeCode = $this->getAttributeModel()->getAttributeCode();

        $productCollection->addFieldToFilter($attributeCode, ["in" => $attributeValue]);
        $this->moduleFilter->setAttributeArray($attributeCode, $request->getParam($this->_requestVar));

        $state = $this->getLayer()->getState();
        foreach ($attributeValue as $value) {
            $label = $this->getOptionText($value);
            $state->addFilter($this->_createItem($label, $value));
        }

        return $this;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData()
    {
        if (!$this->moduleHelper->isEnabled()) {
            return parent::_getItemsData();
        }

        $attribute = $this->getAttributeModel();
        $attributeCode = $attribute->getAttributeCode();
        $productCollection = $this->getProductCollection($attributeCode);
        $attribute = $this->getAttributeModel();

        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        if (count($optionsFacetedData) === 0
            && $this->getAttributeIsFilterable($attribute) !== static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
        ) {
            return $this->itemDataBuilder->build();
        }

        $productSize = $productCollection->getSize();

        $options = $attribute->getFrontend()
            ->getSelectOptions();

        $handleOption = $this->handleOption($options, $optionsFacetedData, $productSize, $attribute);

        if ($handleOption['check_count']) {
            foreach ($handleOption['data'] as $item) {
                if ($attributeCode == 'rating') {
                    $style= 'style="display: inline-block; margin-top: -8px; vertical-align: middle;"';
                    $html = '<div class="rating-summary" '.$style.'>
                                 <div class="rating-result" title="%s">
                                            <span style="width:%s"><span>%u</span></span>
                                 </div>
                              </div>';
                    $rate = ((int)$item['value']*20).'%';
                    $item['label'] = sprintf($html, $rate, $rate, $item['value']);
                }
                $count = $item['count'];
                if (!$this->moduleHelper->isElasticSearchEngine() && $this->getMultiCategoriesCollection()) {
                    $count = $this->getMultiCategoriesCollection()
                        ->addFieldToFilter($attributeCode, ["in" => $item['value']])
                        ->getSize();
                }

                if (empty($count)) continue;

                $this->itemDataBuilder->addItemData($item['label'], $item['value'], $count);
            }
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getMultiCategoriesCollection()
    {
        if (empty($this->getLayer()
            ->getProductCollection()->getAddedFilters()['category_ids'])) {
            return null;
        }
        $categoryIds = $this->getLayer()
            ->getProductCollection()->getAddedFilters()['category_ids'];
        if (is_array($categoryIds) && count($categoryIds) > 1) {
            $collection = $this->collectionProvider->getCollection($this->getLayer()->getCurrentCategory());
            $collection->updateSearchCriteriaBuilder();
            $this->getLayer()->prepareProductCollection($collection);

            foreach ($this->getLayer()
                         ->getProductCollection()->getAddedFilters() as $field => $condition) {
                if ($field == 'category_ids') {
                    $collection->addCategoriesFilter(['in' => $categoryIds]);
                } else {
                    $collection->addFieldToFilter($field, $condition);
                }
            }
            return $collection;
        }
        return null;
    }

    /**
     * @param string $attributeCode
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|object
     */
    protected function getProductCollection($attributeCode)
    {
        $productCollection = $this->getLayer()
            ->getProductCollection();

        /** @var \Bss\LayerNavigation\Model\ResourceModel\Fulltext\Collection $collection */
        $collection = $this->collectionProvider->getCollection($this->getLayer()->getCurrentCategory());
        $collection->updateSearchCriteriaBuilder();
        $this->getLayer()->prepareProductCollection($collection);

        foreach ($productCollection->getAddedFilters() as $field => $condition) {
            if ($attributeCode == $field) {
                continue;
            }
            if ($field == 'category_ids') {
                $collection->multipleCategoriesFilter($condition);
            } else {
                $collection->addFieldToFilter($field, $condition);
            }
        }
        return $collection;
    }

    /**
     * @param $options
     * @param $optionsFacetedData
     * @param $productSize
     * @param $attribute
     * @return mixed
     */
    protected function handleOption($options, $optionsFacetedData, $productSize, $attribute)
    {
        $dataResult['check_count'] = false;
        $dataResult['data'] = [];
        $itemData   = [];
        $checkCount = false;
        foreach ($options as $option) {
            if (empty($option['value'])) {
                continue;
            }

            $value = $option['value'];

            $count = isset($optionsFacetedData[$value]['count'])
                ? (int)$optionsFacetedData[$value]['count']
                : 0;

            if ($this->getAttributeIsFilterable($attribute) == static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
                && (!$this->getFilterModel()->isOptionReducesResults($count, $productSize))
            ) {
                continue;
            }

            if ($count > 0) {
                $checkCount = true;
            }

            $itemData[] = [
                'label' => $this->tagFilter->filter($option['label']),
                'value' => $value,
                'count' => $count
            ];
        }
        $dataResult['check_count'] = $checkCount;
        $dataResult['data'] = $itemData;

        return $dataResult;
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
