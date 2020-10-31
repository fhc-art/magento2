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
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Block\Product\Renderer;

use Magento\Swatches\Block\Product\Renderer\Configurable;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product as CatalogProduct;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
use Bss\ConfigurableProductWholesale\Helper\Data as WholesaleData;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Bss\ConfigurableProductWholesale\Model\ConfigurableData;

/**
 * Class ConfigurableProductWholesale
 *
 * @package Bss\ConfigurableProductWholesale\Block\Product\Renderer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigurableProductWholesale extends Configurable
{
    const WHOLESALE_SWATCHES_TEMPLATE = 'product/view/renderer.phtml';

    const WHOLESALE_TEMPLATE = 'product/view/configurable.phtml';

    const WHOLESALE_JS = 'bss/configurableproductwholesale';

    const WHOLESALE_JS_AJAX = 'bss/configurableproductwholesale_ajax';

    /**
     * @var WholesaleData
     */
    private $helperBss;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @var StockRegistryProviderInterface
     */
    private $stockRegistryProvider;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var /Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * @var CollectionFactory
     */
    private $attrOptionCollectionFactory;

    /**
     * @var ConfigurableProductType
     */
    private $configurableProductType;

    /**
     * @var ConfigurableData
     */
    private $configurableData;


    /**
     * ConfigurableProductWholesale constructor.
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param Data $helper
     * @param CatalogProduct $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param SwatchData $swatchHelper
     * @param Media $swatchMediaHelper
     * @param StockStateInterface $stockState
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param ProductRepository $productRepository
     * @param WholesaleData $helperBss
     * @param CollectionFactory $attrOptionCollectionFactory
     * @param ConfigurableProductType $configurableProductType
     * @param ConfigurableData $configurableData
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        Data $helper,
        CatalogProduct $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        SwatchData $swatchHelper,
        Media $swatchMediaHelper,
        StockStateInterface $stockState,
        StockRegistryProviderInterface $stockRegistryProvider,
        ProductRepository $productRepository,
        WholesaleData $helperBss,
        CollectionFactory $attrOptionCollectionFactory,
        ConfigurableProductType $configurableProductType,
        ConfigurableData $configurableData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $swatchHelper,
            $swatchMediaHelper,
            $data
        );
        $this->helperBss = $helperBss;
        $this->stockState = $stockState;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->productRepository = $productRepository;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->configurableProductType = $configurableProductType;
        $this->configurableData = $configurableData;
    }

    /**
     * Return renderer template wholesale
     *
     * @return string
     */
    public function getRendererTemplate()
    {
        if ($this->helperBss->getConfig()) {
            if ($this->helperBss->validateMagentoVersion('2.1.6')) {
                $hasSwatch = $this->isProductHasSwatchAttribute();
            } else {
                $hasSwatch = $this->isProductHasSwatchAttribute;
            }
            if ($hasSwatch) {
                return self::WHOLESALE_SWATCHES_TEMPLATE;
            } else {
                return self::WHOLESALE_TEMPLATE;
            }
        } else {
            return parent::getRendererTemplate();
        }
    }

    /**
     * @return array
     */
    public function getJsonConfigTable()
    {
        $currentProduct = $this->getProduct();
        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        $tableData = $this->configurableAttributeData->getTableOrdering($currentProduct, $options);
        return $tableData;
    }

    /**
     * @return mixed
     */
    public function getStockItem()
    {
        $productId = $this->getProduct()->getId();
        return $this->stockRegistry->getStockItem($productId);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function getJsonChildInfo()
    {
        $childData = '';
        $allowProducts = $this->getAllowProducts();
        $product = $this->getProduct();
        if (!$this->helperBss->isAjax($product)) {
            $childData = $this->configurableData->getJsonChildInfo($product, [], $allowProducts);
        }
        return $childData;
    }

    /**
     * Get Product Information
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getJsonConfigTableOrdering()
    {
        $store = $this->getCurrentStore();
        $currentProduct = $this->getProduct();

        $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');
        $allowProducts = $this->getAllowProducts();
        $options = $this->helper->getOptions($currentProduct, $allowProducts);

        //fix 2.2
        if ($this->helperBss->validateMagentoVersion('2.2.0')) {
            foreach ($allowProducts as $product) {
                $productId = $product->getId();
                $tableImages = $this->helper->getGalleryImages($product);
                if ($tableImages) {
                    foreach ($tableImages as $image) {
                        $options['images'][$productId][] =
                            [
                                'thumb' => $image->getData('small_image_url'),
                                'img' => $image->getData('medium_image_url'),
                                'full' => $image->getData('large_image_url'),
                                'caption' => $image->getLabel(),
                                'position' => $image->getPosition(),
                                'isMain' => $image->getFile() == $product->getImage(),
                            ];
                    }
                }
            }
        }

        $attributesData = $this->configurableAttributeData->getAttributesDataTableOrdering($currentProduct, $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getBaseAmount()),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'productId' => $currentProduct->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($options['images']) ? $options['images'] : [],
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Get TierPrice Information
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceTableOrdering()
    {
        if ($this->helperBss->checkCustomer('hide_price')) {
            return false;
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->getProduct();
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($storeId, $product);
        $usedProducts = $productTypeInstance->getUsedProducts($product);
        $childrenList = [];
        foreach ($usedProducts as $child) {
            $this->_eventManager->dispatch('bss_prepare_product_price', ['product' => $child]);
            $tierPrices = [];
            $tierPricesExclTax = [];
            $isSaleable = $child->isSaleable();
            if ($isSaleable) {
                $priceAmount = $child->getPriceInfo()->getPrice('final_price')->getAmount();
                $tierPrices[1] = [
                    'finalPrice' => $this->getBssPriceValue($child, $priceAmount->getValue(), false),
                    'exclTaxFinalPrice' => $this->getBssPriceValue($child, $priceAmount->getValue(['tax']), false)
                ];
                $tierPricesList = $this->getBssTierPrice($child);
                if (isset($tierPricesList) && !empty($tierPricesList)) {
                    foreach ($tierPricesList as $price) {
                        $tierPrices[$price['price_qty']] = [
                            'finalPrice' => $price['price']->getValue(),
                            'exclTaxFinalPrice' => $price['price']->getValue(['tax'])
                        ];
                    }
                }
                if (isset($tierPrices) && !empty($tierPrices)) {
                    $childrenList['finalPrice'][$child->getId()] = $tierPrices;
                    $childrenList['exclTaxFinalPrice'][$child->getId()] = $tierPricesExclTax;
                }
            }
        }
        return $this->jsonEncoder->encode($childrenList);
    }

    /**
     * @return array
     * @codingStandardsIgnoreStart
     */
    protected function _getAdditionalConfig()
    {
        $result = parent::_getAdditionalConfig();
        $product = $this->getProduct();
        $this->_eventManager->dispatch('bss_prepare_product_price', ['product' => $product]);
        if ($product->getBssHidePrice()) {
            $result['prices'] = [
                'oldPrice' => [
                    'amount' => $product->getBssHidePriceHtml(),
                ],
                'basePrice' => [
                    'amount' => $product->getBssHidePriceHtml(),
                ],
                'finalPrice' => [
                    'amount' => $product->getBssHidePriceHtml(),
                ],
            ];
        }
        return $result;
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param $product
     * @param $value
     * @return mixed
     */
    private function getBssPriceValue($product, $value)
    {
        if ($product->getBssHidePrice()) {
            return $product->getBssHidePriceHtml();
        } else {
            return $value;
        }
    }

    /**
     * @param $product
     * @return array
     */
    private function getBssTierPrice($product)
    {
        if ($product->getBssHidePrice()) {
            return [];
        } else {
            return $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        }
    }

    /**
     * @return string
     */
    public function getJsFile()
    {
        $product = $this->getProduct();
        $isAjax = $this->helperBss->isAjax($product);
        if ($isAjax) {
            return self::WHOLESALE_JS_AJAX;
        } else {
            return self::WHOLESALE_JS;
        }
    }

    /**
     * Get Count Configurable Product Attributes
     *
     * @return mixed
     */
    public function getCountAttributes()
    {
        $product = $this->getProduct();
        return $this->configurableData->countAttributes($product);
    }

    /**
     * Check version
     *
     * @return bool
     */
    public function checkVersion()
    {
        return $this->helperBss->validateMagentoVersion('2.2.7');
    }
}
