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

namespace Bss\ConfigurableProductWholesale\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData as WholesaleModel;
use Bss\ConfigurableProductWholesale\Helper\Data as WholesaleData;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Catalog\Helper\Product;

/**
 * Class ConfigurableData
 *
 * @package Bss\ConfigurableProductWholesale\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigurableData
{
    /**
     * @var ConfigurableProductType
     */
    private $configurableProductType;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var StockRegistryProviderInterface
     */
    private $stockRegistryProvider;

    /**
     * @var WholesaleData
     */
    private $helperBss;

    /**
     * @var CollectionFactory
     */
    private $attrOptionCollectionFactory;

    /**
     * @var Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    private $catalogProduct = null;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @var WholesaleModel
     */
    protected $configurableAttributeData;

    /**
     * @param ConfigurableProductType $configurableProductType
     * @param ProductRepository $productRepository
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param WholesaleModel $configurableAttributeData
     * @param WholesaleData $helperBss
     * @param CollectionFactory $attrOptionCollectionFactory
     * @param Product $catalogProduct
     * @param Data $helper
     * @param StockStateInterface $stockState
     */
    public function __construct(
        ConfigurableProductType $configurableProductType,
        ProductRepository $productRepository,
        StockRegistryProviderInterface $stockRegistryProvider,
        WholesaleModel $configurableAttributeData,
        WholesaleData $helperBss,
        CollectionFactory $attrOptionCollectionFactory,
        Product $catalogProduct,
        Data $helper,
        StockStateInterface $stockState
    ) {
        $this->configurableProductType = $configurableProductType;
        $this->productRepository = $productRepository;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->configurableAttributeData = $configurableAttributeData;
        $this->helperBss = $helperBss;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->catalogProduct = $catalogProduct;
        $this->helper = $helper;
        $this->stockState = $stockState;
    }

    /**
     * @param $product
     * @param array $mergedIds
     * @param null $allowProducts
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function getJsonChildInfo($product, $mergedIds = [], $allowProducts = null)
    {
        $code = $this->getJsonConfigTable($product, $allowProducts);
        $childData = $this->getConfigChildProductIds($product, $mergedIds, $code['code']);
        return $this->helperBss->getJsonHelper()->jsonEncode($childData);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param mixed|null $allowProducts
     * @return array
     */
    public function getJsonConfigTable($product, $allowProducts = null)
    {
        if (!$allowProducts) {
            $allowProducts = $this->getAllowProducts($product);
        }
        $options = $this->helper->getOptions($product, $allowProducts);
        $tableData = $this->configurableAttributeData->getTableOrdering($product, $options);
        return $tableData;
    }

    /**
     * @param $product
     * @param $mergedIds
     * @param null $code
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function getConfigChildProductIds($product, $mergedIds, $code = null)
    {
        if (!isset($product)) {
            return fasle;
        }
        $showOutOfStockConfig = $this->helperBss->getDisplayOutOfStock();
        $storeId = $this->helperBss->getMagentoHelper()->getStoreId();
        $usedProducts = $this->configurableProductType->getUsedProductCollection($product)
            ->addAttributeToSelect('*')->addStoreFilter($storeId);
        if (!empty($mergedIds)) {
            $usedProducts->addFieldToFilter('entity_id', ['in' => $mergedIds]);
        }
        $childrenList = [];
        $options = $this->helper->getOptions($product, $this->getAllowProducts($product));
        $attributesDatas = $this->configurableAttributeData->getAttributesData($product, $options);
        foreach ($usedProducts as $productChild) {
            $isSaleable = $productChild->isSaleable();
            if ($isSaleable || $showOutOfStockConfig) {
                $childrenList[] = $this->getConfigProduct($productChild, $attributesDatas, $code);
            }
        }

        uasort($childrenList, function ($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });

        $childrenList = array_values($childrenList);
        return $childrenList;
    }

    /**
     * @param $productChild
     * @param $attributesDatas
     * @param null $code
     * @return mixed
     * @throws \Zend_Currency_Exception
     */
    public function getConfigProduct($productChild, $attributesDatas, $code = null)
    {
        $childProductId = $productChild->getId();
        $this->helperBss->getEventManager()->dispatch('bss_prepare_product_price', ['product' => $productChild]);
        $optionId = $productChild->getData($code);
        $websiteId = $productChild->getStore()->getWebsiteId();
        $stockItem = $this->stockRegistryProvider->getStockItem($childProductId, $websiteId);
        $stock = $stockItem->getIsInStock();
        $attr = $productChild->getResource()->getAttribute($code);
        $optionText = $this->addOptionText($attr, $optionId);
        $sortOrder = $this->sortOptions($attr, $optionId);
        if (!empty($dataOptions = $this->pushOptions($attributesDatas, $productChild))) {
            $children['option'] = $dataOptions;
        }

        // get tier price
        if ($this->helperBss->hasDisplayAttribute('tier_price')) {
            $children['tier_price'] = $this->pushTierPrice($productChild);
        }

        $status = $this->getStatus($stock, $childProductId, $websiteId);
        $children['attribute'] = $optionText;

        if ($this->helperBss->hasDisplayAttribute('sku')) {
            $children['sku'] = $productChild->getSku();
        }

        if ($this->helperBss->hasDisplayAttribute('availability')) {
            $children['qty_stock'] = $status;
        }

        if (!empty($priceData = $this->pushPrice($productChild))) {
            $children['price'] = $priceData;
        }

        $stockItem = $this->helperBss->getMagentoHelper()
        ->getStockRegistry()->getStockItem($childProductId, $websiteId);
        $children['other'] = $this->pushMinMaxQty($stockItem);
        $children['other']['product_id'] = $childProductId;
        $children['other']['qty'] = 0;
        $children['status_stock'] = $stock;
        $children['attribute_code'] = $code;
        $children['attribute_id'] = $attr->getId();
        $children['option_id'] = $optionId;
        $children['sort_order'] = $sortOrder;
        return $children;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function countAttributes($product)
    {
        $options = $this->helper->getOptions($product, $this->getAllowProducts($product));
        $attributesDatas = $this->configurableAttributeData->getAttributesData($product, $options);
        return count($attributesDatas['attributes']);
    }

    /**
     * @param mixed $attr
     * @param int $optionId
     * @return string
     */
    private function addOptionText($attr, $optionId)
    {
        $optionText = '';
        if ($attr->usesSource()) {
            $optionText = $attr->getSource()->getOptionText($optionId);
        }
        return $optionText;
    }

    /**
     * @param mixed $attr
     * @param int $optionId
     * @return int
     */
    private function sortOptions($attr, $optionId)
    {
        $sortOrder = '';
        $optionCollection = $this->attrOptionCollectionFactory->create();
        $option = $optionCollection->setAttributeFilter(
            $attr->getId()
        )->setPositionOrder(
            'asc',
            true
        )->addFieldToFilter(
            'main_table.option_id',
            ['eq' => $optionId]
        );
        $optionData = $option->getData();
        if (!empty($optionData) && !empty($optionData[0])) {
            $sortOrder = $optionData[0]['sort_order'];
        }
        return $sortOrder;
    }

    /**
     * @param array $attributesDatas
     * @param \Magento\Catalog\Model\ProductRepository $productChild
     * @return array
     */
    private function pushOptions($attributesDatas, $productChild)
    {
        $dataOptions = [];
        if (isset($attributesDatas) && !empty($attributesDatas['attributes'])) {
            foreach ($attributesDatas['attributes'] as $attributesData) {
                $codeAttr = $attributesData['code'];
                $idAttr = $attributesData['id'];

                $codeProduct = $productChild->getData($codeAttr);
                if (isset($codeProduct)) {
                    $dataOptions['data-option-' . $idAttr] = $codeProduct;
                }
            }
        }
        return $dataOptions;
    }

    /**
     * @param $child
     * @return string
     * @throws \Zend_Currency_Exception
     */
    private function pushTierPrice($child)
    {
        $tierPriceModel = $child->getPriceInfo()->getPrice('tier_price');
        $tierPricesList = $tierPriceModel->getTierPriceList();
        if ($child->getBssHidePrice()) {
            $tierPricesList = [];
        }
        $detailedPrice = '';
        $tierPriceHtml = '';
        if (isset($tierPricesList) && !empty($tierPricesList)) {
            foreach ($tierPricesList as $index => $price) {
                $detailedPrice .= '<li class="item">';
                $detailedPrice .= __(
                    'Buy %1 for %2 each and 
                        <strong class="benefit">save<span class="percent tier-%3"> %4</span>%</strong></li>',
                    $price['price_qty'],
                    $this->helperBss->getFormatPrice($price['price']->getValue()),
                    $index,
                    $tierPriceModel->getSavePercent($price['price'])
                );
                $detailedPrice .= '</li>';
            }
        }
        if ($detailedPrice != '' && !$this->helperBss->checkCustomer('hide_price')) {
            $tierPriceHtml = '<ul class="prices-tier items">' . $detailedPrice . '</ul>';
        }
        return $tierPriceHtml;
    }

    /**
     * @param mixed $stockItem
     * @return array
     */
    private function pushMinMaxQty($stockItem)
    {
        $data = [];
        $minSaleQty = (float) $stockItem->getMinSaleQty();
        $maxSaleQty = (float) $stockItem->getMaxSaleQty();
        if (isset($minSaleQty) && $minSaleQty > 0) {
            $data['min_qty'] = $minSaleQty;
        }
        if (isset($maxSaleQty) && $maxSaleQty > 0) {
            $data['max_qty'] = $maxSaleQty;
        }
        return $data;
    }

    /**
     * Get Allowed Products
     *
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getAllowProducts($product)
    {
        $products = [];
        $showOutOfStockConfig = $this->helperBss->getDisplayOutOfStock();
        $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
        $allProducts = $product->getTypeInstance()->getUsedProducts($product, null);
        foreach ($allProducts as $product) {
            if ($product->isSaleable() || $skipSaleableCheck || $showOutOfStockConfig) {
                $products[] = $product;
            }
        }
        return $products;
    }

    /**
     * @param bool $stock
     * @param int $childProductId
     * @param int $websiteId
     * @return string|int
     */
    private function getStatus($stock, $childProductId, $websiteId)
    {
        if ($stock) {
            if (!$this->helperBss->getConfig('stock_number')) {
                $status = __('In stock');
            } else {
                $status = $this->stockState->getStockQty($childProductId, $websiteId);
            }
        } else {
            $status = __('Out of stock');
        }
        return $status;
    }

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productChild
     * @return array
     */
    private function pushPrice($productChild)
    {
        $priceData = [];
        if ($this->helperBss->hasDisplayAttribute('unit_price') &&
            !$this->helperBss->checkCustomer('hide_price')
        ) {
            $finalPriceAmount = $productChild->getPriceInfo()->getPrice('final_price')->getAmount();
            $regularPriceAmount = $productChild->getPriceInfo()->getPrice('regular_price')->getAmount();
            if (!$productChild->getBssHidePrice()) {
                $finalPrice = $finalPriceAmount->getValue();
                $price = $regularPriceAmount->getValue();
                $exclTaxFinalPrice = $finalPriceAmount->getValue(['tax']);
                $exclTaxPrice = $regularPriceAmount->getValue(['tax']);
                $priceData['final_price'] = $finalPrice;
                $priceData['excl_tax_final_price'] = $exclTaxFinalPrice;
                if ($price != $finalPrice) {
                    $priceData['old_price'] = $price;
                    $priceData['excl_tax_old_price'] = $exclTaxPrice;
                }
            } else {
                $priceData['final_price'] = $productChild->getBssHidePriceHtml();
                $priceData['excl_tax_final_price'] = '';
            }
            $priceData['saleable'] = !$productChild->getBssDisableCart();
        }
        return $priceData;
    }
}
