<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * @category   BSS
 * @package    Bss_AvaTaxCustom
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AvaTaxCustom\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;

class HideStock extends \Magento\Catalog\Block\Product\View
{
	protected $stockRegistry;

	protected $bssHelperStock;

	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Bss\AvaTaxCustom\Helper\Stock $bssHelperStock,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->stockRegistry = $stockRegistry;
        $this->bssHelperStock = $bssHelperStock;
    }

    public function isBackorderProduct()
    {
    	$product = $this->getProduct();
    	if ($product->getTypeId() == "simple" || $product->getTypeId() == "virtual") {
    		$stockitem = $this->stockRegistry->getStockItem($product->getId());
    		$qty = $stockitem->getQty();
    		$backOrderGlobal = $this->bssHelperStock->isBackorderGlobal();
    		if ($backOrderGlobal && !$qty) {
    			return true;
    		}
    	}
    	
    	return false;
    }

 	public function isBackorderGlobal()
 	{
 		return $this->bssHelperStock->isBackorderGlobal();
 	}
}