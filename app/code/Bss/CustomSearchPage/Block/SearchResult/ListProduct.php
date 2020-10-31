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
 * @package   Bss_CustomSearchPage
 * @author    Extension Team
 * @copyright Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomSearchPage\Block\SearchResult;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct as CoreListProduct;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Layer\Search;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

/**
 * Class ListProduct
 *
 * Custom class for additional function over original
 */
class ListProduct extends CoreListProduct
{
    /**
     * @var Search
     */
    protected $catalogLayer;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    protected $salableQty;

    /**
     * ListProduct constructor.
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param Search $catalogLayer
     * @param StockRegistryInterface $stockRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        Search $catalogLayer,
        StockRegistryInterface $stockRegistry,
        GetProductSalableQtyInterface $salableQty,
        array $data = []
    ) {
        $this->catalogLayer = $catalogLayer;
        $this->stockRegistry = $stockRegistry;
        $this->salableQty = $salableQty;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Get backorder status by stock.
     *
     * @param Product $product
     * @return bool
     */
    public function getBackOrderStatus($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        if ($stockItem) {
            return $stockItem->getBackorders();
        }
        return false;
    }

    /**
     * Get product Quantity.
     *
     * @param Product $product
     * @return integer
     */
    public function getProductQty($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        return $stockItem->getQty();
    }

    /**
     * Get product salable quantity
     *
     * @param Product $product
     * @return integer
     */
    public function getSalableQty($product)
    {
        return $this->salableQty->execute($product->getSku(), $product->getStore()->getWebsiteId());
    }
}
