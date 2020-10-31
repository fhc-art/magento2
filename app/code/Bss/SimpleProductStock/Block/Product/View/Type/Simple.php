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
 * @category   BSS
 * @package    Bss_SimpleProductStock
 * @author     Extension Team
 * @copyright  Copyright (c) 2019-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\SimpleProductStock\Block\Product\View\Type;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\Type\Simple as CoreSimple;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * Class Simple
 *
 * @package Bss\SimpleProductStock\Block\Product\View\Type
 */
class Simple extends CoreSimple
{
    /**
     * @var StockRegistry
     */
    protected $stockRegistry;

    /**
     * Simple constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param StockRegistry $stockRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        StockRegistry $stockRegistry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Get product stock;
     *
     * @return float
     */
    public function getProductStock()
    {
        $stock = $this->stockRegistry->getStockItem($this->getProduct()->getId());
        return $stock->getQty();
    }
}
