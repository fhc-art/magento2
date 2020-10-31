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
 * @package   Bss_Simpledetailconfigurable
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\Simpledetailconfigurable\Model\Product\Type;

use Bss\Simpledetailconfigurable\Helper\ModuleConfig;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config;
use Magento\ConfigurableProduct\Model\Product\Type\Collection\SalableProcessor;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as MagentoConfigurable;
use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Configurable extends MagentoConfigurable
{
    /**
     * @var SearchCriteriaBuilder|null
     */
    private $searchCriteriaBuilder;

    /**
     * @var ProductAttributeRepositoryInterface|null
     */
    private $productAttributeRepository;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Configurable constructor.
     * @codingStandardsIgnoreStart/End
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory
     * @param MagentoConfigurable\AttributeFactory $configurableAttributeFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ModuleConfig $moduleConfig
     * @param \Magento\Framework\Cache\FrontendInterface|null $cache
     * @param \Magento\Customer\Model\Session|null $customerSession
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param ProductInterfaceFactory|null $productFactory
     * @param SalableProcessor|null $salableProcessor
     * @param ProductAttributeRepositoryInterface|null $productAttributeRepository
     * @param SearchCriteriaBuilder|null $searchCriteriaBuilder
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttributeFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Bss\Simpledetailconfigurable\Helper\ModuleConfig $moduleConfig,
        \Magento\Framework\Cache\FrontendInterface $cache = null,
        \Magento\Customer\Model\Session $customerSession = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        ProductInterfaceFactory $productFactory = null,
        SalableProcessor $salableProcessor = null,
        ProductAttributeRepositoryInterface $productAttributeRepository = null,
        SearchCriteriaBuilder $searchCriteriaBuilder = null
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository,
            $typeConfigurableFactory,
            $eavAttributeFactory,
            $configurableAttributeFactory,
            $productCollectionFactory,
            $attributeCollectionFactory,
            $catalogProductTypeConfigurable,
            $scopeConfig,
            $extensionAttributesJoinProcessor,
            $cache,
            $customerSession,
            $serializer,
            $productFactory,
            $salableProcessor,
            $productAttributeRepository,
            $searchCriteriaBuilder
        );
    }

    /**
     * Returns array of sub-products for specified configurable product
     * Result array contains all children for specified configurable product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $requiredAttributeIds Attributes to include in the select; one-dimensional array
     * @return ProductInterface[]
     */
    public function getUsedProducts($product, $requiredAttributeIds = null)
    {
        if (!$product->hasData($this->_usedProducts)) {
            $collection = $this->getConfiguredUsedProductCollection($product, false, $requiredAttributeIds);
            $usedProducts = array_values($collection->getItems());
            $product->setData($this->_usedProducts, $usedProducts);
        }

        return $product->getData($this->_usedProducts);
    }

    /**
     * Retrieve related products collection
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     */
    public function getUsedProductCollection($product)
    {
        $collection = $this->_productCollectionFactory->create()->setFlag(
            'product_children',
            true
        )->setProductFilter(
            $product
        );
        if (null !== $this->getStoreFilter($product)) {
            $collection->addStoreFilter($this->getStoreFilter($product));
        }

        if ($this->moduleConfig->isModuleEnable()
            && !$this->moduleConfig->isEnableChildOption()) {
            $collection
                ->addFilterByRequiredOptions()
                ->addAttributeToFilter('has_options', [['neq' => 1], ['null' => true]], 'left');
        } elseif (!$this->moduleConfig->isModuleEnable()) {
            $collection->addFilterByRequiredOptions();
        }
        
        return $collection;
    }

    /**
     * Prepare collection for retrieving sub-products of specified configurable product
     * Retrieve related products collection with additional configuration
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $skipStockFilter
     * @param array $requiredAttributeIds Attributes to include in the select
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getConfiguredUsedProductCollection(
        \Magento\Catalog\Model\Product $product,
        $skipStockFilter = true,
        $requiredAttributeIds = null
    ) {
        $collection = $this->getUsedProductCollection($product);

        if ($skipStockFilter) {
            $collection->setFlag('has_stock_status_filter', true);
        }

        $attributesForSelect = $this->getAttributesForCollection($product);
        if ($requiredAttributeIds) {
            $this->searchCriteriaBuilder->addFilter('attribute_id', $requiredAttributeIds, 'in');
            $requiredAttributes = $this->productAttributeRepository
                ->getList($this->searchCriteriaBuilder->create())->getItems();
            $requiredAttributeCodes = [];
            foreach ($requiredAttributes as $requiredAttribute) {
                $requiredAttributeCodes[] = $requiredAttribute->getAttributeCode();
            }
            $attributesForSelect = array_unique(array_merge($attributesForSelect, $requiredAttributeCodes));
        }

        $collection->addAttributeToSelect($attributesForSelect);

        $collection->setStoreId($product->getStoreId());

        $collection->addMediaGalleryData();
        $collection->addTierPriceData();

        return $collection;
    }

    /**
     * Get Config instance
     * @return Config
     */
    protected function getCatalogConfig()
    {
        if (!$this->catalogConfig) {
            $this->catalogConfig = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->catalogConfig;
    }

    /**
     * @return array
     */
    protected function getAttributesForCollection(\Magento\Catalog\Model\Product $product)
    {
        $productAttributes = $this->getCatalogConfig()->getProductAttributes();

        $requiredAttributes = [
            'name',
            'price',
            'weight',
            'image',
            'thumbnail',
            'status',
            'visibility',
            'media_gallery'
        ];

        $usedAttributes = array_map(
            function ($attr) {
                return $attr->getAttributeCode();
            },
            $this->getUsedProductAttributes($product)
        );

        return array_unique(array_merge($productAttributes, $requiredAttributes, $usedAttributes));
    }
}
