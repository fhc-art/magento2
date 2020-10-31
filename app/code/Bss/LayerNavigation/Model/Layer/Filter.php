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
namespace Bss\LayerNavigation\Model\Layer;

use Magento\Framework\App\RequestInterface;
use Bss\LayerNavigation\Helper\Data as LayerHelper;
use Magento\Swatches\Helper\Data as SwatchHelper;
use Magento\Store\Model\StoreManagerInterface;

class Filter
{

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $sliderTypes = [
        LayerHelper::FILTER_TYPE_SLIDER,
        LayerHelper::FILTER_TYPE_RANGE
    ];

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var LayerHelper
     */
    protected $helper;

    /**
     * @var SwatchHelper
     */
    protected $swatchHelper;

    /**
     * @var array
     */
    protected $attributeArray;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;
    
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Filter constructor.
     * @param RequestInterface $request
     * @param LayerHelper $layerHelper
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param SwatchHelper $swatchHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     */
    public function __construct(
        RequestInterface $request,
        LayerHelper $layerHelper,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        SwatchHelper $swatchHelper,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogHelper
    ) {
        $this->serialize = $serialize;
        $this->productMetadata = $productMetadata;
        $this->request = $request;
        $this->helper = $layerHelper;
        $this->swatchHelper = $swatchHelper;
        $this->storeManager = $storeManager;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeArray()
    {
        return $this->attributeArray;
    }

    /**
     * @inheritdoc
     */
    public function setAttributeArray($key, $value)
    {
        if (!isset($this->attributeArray[$key])) {
            $this->attributeArray[$key] = $value;
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getLayerConfiguration($filters, $config)
    {
        $slider = $this->handleSlider($filters);

        $config->setData('slider', $slider);
        $active = $config->getActive();
        $swatchOptionText  = [];
        $multipleAttribute = [];
        $lessMore = [];
        foreach ($filters as $filter) {
            $requestVar = $filter->getRequestVar();
            if (!in_array($requestVar, $active) && $this->checkExpand($filter)) {
                $active[] = $requestVar;
            }
            if ($this->isMultiple($filter)) {
                $multipleAttribute[] = $filter->getRequestVar();
            }
            if ($this->getFilterType($filter, LayerHelper::FILTER_TYPE_SWATCHTEXT)) {
                $swatchOptionText[] = $filter->getRequestVar();
            }
            if ($this->checkLessMore($filter)) {
                $lessMore[] = $filter->getRequestVar();
            }
        }

        $config->addData([
            'scroll' => true,
            'active' => $active,
            'lessMore' => [
                'status' => $this->lessMore(),
                'disable' => $lessMore
            ],
            'useAjax' => $this->useAjax(),
            'buttonSubmit' => $this->initButtonSubmit(),
            'multipleAttrs' => $multipleAttribute,
            'swatchOptionText' => $swatchOptionText
        ]);

        return $this;
    }

    /**
     * @param $filters
     * @return array
     */
    protected function handleSlider($filters)
    {
        $slider = [];
        foreach ($filters as $filter) {
            if ($this->isSliderTypes($filter) && $filter->getItemsCount()) {
                $slider[$filter->getRequestVar()] = $filter->getSliderConfig();
            }
        }
        return $slider;
    }

    /**
     * @return array
     */
    protected function initButtonSubmit()
    {
        $enable = $this->helper->isButtonSubmit();

        if ($this->request->getFullActionName() == 'catalogsearch_result_index') {
            $urlSuffix = '';
        } else {
            $urlSuffix = $this->helper->getUrlSuffix();
        }
        $seoUrlEnable = true;
        $submitResult = [
            'enable' => $enable,
            'seoUrlEnable' => $seoUrlEnable,
            'urlSuffix' => $urlSuffix,
            'baseUrl' =>
                trim($this->storeManager->getStore()->getBaseUrl(), '/')
                . '/' . trim($this->request->getOriginalPathInfo(), '/')
        ];

        return $submitResult;
    }

    /**
     * @return int|mixed
     */
    public function lessMore()
    {
        $lessMore = $this->helper->getLessMore();
        $lessMore = (int)$lessMore;
        return $lessMore;
    }

    /**
     * @return mixed
     */
    public function useAjax()
    {
        $useAjax = $this->helper->getUserAjax();
        return $useAjax;
    }

    /**
     * @param $filter
     * @return bool
     */
    public function checkLessMore($filter)
    {
        $statusLessMore = $this->getLayerProperty($filter, LayerHelper::FIELD_SHOWMORE);
        if ((int)$statusLessMore == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $filter
     * @return bool
     */
    public function checkExpand($filter)
    {
        $statusExpand = $this->getLayerProperty($filter, LayerHelper::FIELD_IS_EXPAND);
        $requestVar = $filter->getRequestVar();
        if ((int)$statusExpand == 2 || $requestVar == 'cat' || $requestVar == 'rating') {
            $config = $this->helper->isCollapse();
            return $config ? false : true;
        } elseif ((int)$statusExpand == 0) {
            return true;
        } elseif ((int)$statusExpand == 1) {
            return false;
        } else {
            return false;
        }
    }

    /**
     * @param $filter
     * @param null $types
     * @return bool
     */
    public function isSliderTypes($filter, $types = null)
    {
        $filterType = $this->getFilterType($filter);
        $types = $types ?: $this->sliderTypes;
        return in_array($filterType, $types);
    }

    /**
     * @param $filter
     * @param $field
     * @return mixed
     */
    protected function getLayerProperty($filter, $field)
    {
        if ($filter->hasAttributeModel()) {
            $attribute = $filter->getAttributeModel();
            $this->prepareAttributeData($attribute);

            $fieldValue = $attribute->getData($field);
            return $fieldValue;
        }

        return true;
    }

    /**
     * @param $attribute
     */
    public function prepareAttributeData($attribute)
    {
        $version = $this->productMetadata->getVersion();

        $versionArray = explode(".", $version);

        if ($data = $attribute->getAdditionalData()) {
            if ($versionArray[1] < LayerHelper::MAGENTO_VERSION_220) {
                $additionalData = $this->serialize->unserialize($data);
            } else {
                $additionalData = json_decode($data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Unable to unserialize value.');
                }
            }

            if (is_array($additionalData)) {
                $attribute->addData($additionalData);
            }
        }

        if ($attribute->getData(LayerHelper::FIELD_ALLOW_MULTIPLE) === null) {
            $attribute->setData(LayerHelper::FIELD_ALLOW_MULTIPLE, 2);
        }

        if ($attribute->getData(LayerHelper::FIELD_SEARCH_ENABLE) === null) {
            $attribute->setData(LayerHelper::FIELD_SEARCH_ENABLE, 2);
        }

        if ($attribute->getData(LayerHelper::FIELD_IS_EXPAND) === null) {
            $attribute->setData(LayerHelper::FIELD_IS_EXPAND, 2);
        }
    }

    /**
     * @param $filter
     * @param null $compareType
     * @return bool|string
     */
    public function getFilterType($filter, $compareType = null)
    {
        $type = LayerHelper::FILTER_TYPE_LIST;

        if ($filter->hasFilterType()) {
            $type = $filter->getFilterType();
        } elseif ($filter->hasAttributeModel()) {
            $attribute = $filter->getAttributeModel();
            $this->prepareAttributeData($attribute);

            $filterType = $attribute->getData(LayerHelper::FIELD_FILTER_TYPE);
            if (!$filterType) {
                $filterType = $this->handleFilterType($attribute, $filterType);
            }

            $type = $filterType ?: $type;
        }

        return $compareType ? ($type == $compareType) : $type;
    }

    public function handleFilterType($attribute, $filterType)
    {
        switch ($attribute->getData('frontend_input')) {
            case 'text':
            case 'price':
                $filterType = LayerHelper::FILTER_TYPE_SLIDER;
                break;
            case 'select':
                if ($this->swatchHelper->isVisualSwatch($attribute) || $this->swatchHelper->isTextSwatch($attribute)) {
                    $filterType = LayerHelper::FILTER_TYPE_SWATCH;
                }
                break;
            case 'multiselect':
                $filterType = LayerHelper::FILTER_TYPE_LIST;
                break;
        }

        return $filterType;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getItemUrl($item)
    {
        if ($this->isSelected($item)) {
            return $item->getRemoveUrl();
        }

        return $item->getUrl();
    }

    /**
     * @return bool
     */
    public function isSearchEnable()
    {
        return true;
    }

    /**
     * @param $item
     * @return bool
     */
    public function isSelected($item)
    {
        $filterValue = $this->getFilterValue($item->getFilter());

        if (!empty($filterValue) && in_array($item->getValue(), $filterValue)) {
            return true;
        }

        return false;
    }

    /**
     * @param $filter
     * @param bool $explode
     * @return array|mixed
     */
    public function getFilterValue($filter, $explode = true)
    {
        $filterValue = $this->request->getParam($filter->getRequestVar());
        if (empty($filterValue)) {
            return [];
        }

        return $explode ? explode('_', $filterValue) : $filterValue;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isShowCounter()
    {
        return $this->catalogHelper->shouldDisplayProductCountOnLayer();
    }

    /**
     * @param $filter
     * @return bool
     */
    public function isMultiple($filter)
    {
        if ($filter->hasMultipleMode()) {
            return $filter->getMultipleMode();
        } elseif ($filter->hasAttributeModel()) {
            $attribute = $filter->getAttributeModel();
            if (($attribute->getFrontendInput() == 'price') ||
                ($attribute->getBackendType() == 'decimal')
            ) {
                return false;
            }
        }
        return $this->getLayerProperty($filter, LayerHelper::FIELD_ALLOW_MULTIPLE);
    }

    /**
     * @param $optionCount
     * @param $totalSize
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isOptionReducesResults($optionCount, $totalSize)
    {
        return $optionCount && $totalSize;
    }

    /**
     * @return bool
     */
    public function isShowZero($attribute)
    {
        return ($attribute->getData('is_filterable') || $attribute->getData('is_filterable_in_search'));
    }
}
