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
namespace Bss\ConfigurableProductWholesale\Plugin\Model;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Math\Division as MathDivision;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Bss\ConfigurableProductWholesale\Helper\Data as Helper;

class StockStateProvider
{
    /**
     * @var Configurable
     */
    private $configurableProduct;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var MathDivision
     */
    private $mathDivision;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @param Configurable $configurableProduct
     * @param StockItemRepository $stockItemRepository
     * @param MathDivision $mathDivision
     * @param Helper $helper
     */
    public function __construct(
        Configurable $configurableProduct,
        StockRegistryInterface $stockRegistry,
        MathDivision $mathDivision,
        Helper $helper
    ) {
        $this->configurableProduct = $configurableProduct;
        $this->stockRegistry = $stockRegistry;
        $this->mathDivision = $mathDivision;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockStateProvider $subject
     * @param callable $proceed
     * @param StockItemInterface $stockItem
     * @param int|float $qty
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCheckQtyIncrements(
        \Magento\CatalogInventory\Model\StockStateProvider $subject,
        $proceed,
        StockItemInterface $stockItem,
        $qty
    ) {
        $result = $proceed($stockItem, $qty);
        if ($this->helper->getConfig() && !$result->getHasError()) {
            $parentId = $this->configurableProduct->getParentIdsByChild($stockItem->getProductId());
            if (isset($parentId[0])) {
                $increment = $this->stockRegistry->getStockItem($parentId[0])->getQtyIncrements() * 1;
                if ($increment && $this->mathDivision->getExactDivision($qty, $increment) != 0) {
                    $result->setHasError(true);
                    $result->setMessage(__('You can buy this product only in quantities of %1 at a time.', $increment));
                }
            }
        }
        return $result;
    }
}
