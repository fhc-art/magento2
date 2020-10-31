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
namespace Bss\LayerNavigation\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FILTER_TYPE_SLIDER = 'slider';
    const FILTER_TYPE_LIST = 'list';
    const FIELD_ALLOW_MULTIPLE = 'allow_multiple';
    const FIELD_FILTER_TYPE = 'filter_type';
    const FIELD_SEARCH_ENABLE = 'search_enable';
    const FIELD_IS_EXPAND = 'is_expand';
    const FILTER_TYPE_DROPDOWN = 'dropdown';
    const FILTER_TYPE_SLIDERRANGE = 'sliderrange';
    const FILTER_TYPE_RANGE = 'range';
    const FILTER_TYPE_SWATCH = 'swatch';
    const FILTER_TYPE_SWATCHTEXT = 'swatchtext';
    const FILTER_TYPE_RATING = 'rating';
    const FIELD_SHOWMORE = 'show_more';

    const MAGENTO_VERSION_220 = 2;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $encoderInterface;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Search\Model\EngineResolver
     */
    protected $engineResolver;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Json\EncoderInterface $encoderInterface
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Search\Model\EngineResolver $engineResolver
     */
    public function __construct(
        Context $context,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Json\EncoderInterface $encoderInterface,
        StoreManagerInterface $storeManager,
        \Magento\Search\Model\EngineResolver $engineResolver
    ) {
        $this->encoderInterface = $encoderInterface;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->storeManager = $storeManager;
        $this->engineResolver = $engineResolver;
        parent::__construct($context);
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getFilterConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';
        return $this->getConfigValue('layered_navigation/filter' . $code, $storeId);
    }

    /**
     * @return array
     */
    public function getLayerAdditionalFields()
    {
        return [
            self::FIELD_ALLOW_MULTIPLE,
            self::FIELD_FILTER_TYPE,
            self::FIELD_SEARCH_ENABLE,
            self::FIELD_IS_EXPAND,
            self::FIELD_SHOWMORE
        ];
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'layered_navigation/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isRating($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'layered_navigation/general/rating',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isCollapse($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            "layered_navigation/general/is_expand",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isButtonSubmit($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            "layered_navigation/general/button_submit",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getLessMore($storeId = null)
    {
        return $this->scopeConfig->getValue(
            "layered_navigation/general/less_more",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getUserAjax($storeId = null)
    {
        return $this->scopeConfig->getValue(
            "layered_navigation/general/use_ajax",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getCategoryLevel()
    {
        return $this->scopeConfig->getValue(
            "layered_navigation/general/category_level",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getCustomCss($storeId = null)
    {
        return $this->scopeConfig->getValue(
            "layered_navigation/custom_css/custom",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return array
     */
    public function getLayerAttrParams()
    {
        return [
            'displayOptions'        => $this->displayOptions(),
            'displayRule'          => $this->displayRule(),
            'optionDisplayEl'      => '#' . self::FIELD_FILTER_TYPE,
            'allowMultipleInputEL' => '#attribute-' . self::FIELD_ALLOW_MULTIPLE . '-container',
            'searchEnableInputEl'  => '#attribute-' . self::FIELD_SEARCH_ENABLE . '-container'
        ];
    }

    /**
     * @return array
     */
    public function displayOptions()
    {
        $displayTypes = $this->getDisplayTypes();

        $options = [];
        foreach ($displayTypes as $key => $type) {
            if (isset($type['label'])) {
                $options[$key] = $type['label'];
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function displayRule()
    {
        return [
            'price'  => [
                self::FILTER_TYPE_SLIDER,
                self::FILTER_TYPE_RANGE,
                self::FILTER_TYPE_LIST
            ],
            'select' => [
                self::FILTER_TYPE_LIST,
                self::FILTER_TYPE_DROPDOWN
            ],
            'swatch' => [
                self::FILTER_TYPE_SWATCH,
                self::FILTER_TYPE_SWATCHTEXT,
                self::FILTER_TYPE_LIST,
                self::FILTER_TYPE_DROPDOWN
            ]
        ];
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getUrlSuffix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getDisplayTypes()
    {
        $displayTypes = $this->dataObjectFactory->create();
        $displayTypes->setData(
            [
                self::FILTER_TYPE_LIST => [
                    'label' => __('List'),
                    'class' => \Bss\LayerNavigation\Block\Type\OptionList::class
                ],
                self::FILTER_TYPE_DROPDOWN => [
                    'label' => __('Dropdown'),
                    'class' => \Bss\LayerNavigation\Block\Type\Dropdown::class
                ],
                self::FILTER_TYPE_SLIDER => [
                    'label' => __('Slider'),
                    'class' => \Bss\LayerNavigation\Block\Type\Slider::class
                ],
                self::FILTER_TYPE_RANGE => [
                    'label' => __('Range'),
                    'class' => \Bss\LayerNavigation\Block\Type\Slider::class
                ],
                self::FILTER_TYPE_SWATCH => [
                    'label' => __('Swatch')
                ],
                self::FILTER_TYPE_SWATCHTEXT => [
                    'label' => __('Swatch and Text')
                ],
            ]
        );

        $this->_eventManager->dispatch('layer_option_display_type_list', ['type' => $displayTypes]);

        return $displayTypes->getData();
    }

    /**
     * @return bool
     */
    public function isElasticSearchEngine()
    {
        return (strpos($this->engineResolver->getCurrentSearchEngine(), 'elasticsearch') !== false);
    }
}
