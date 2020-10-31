namespace FHC\ShowStockStatus\Helper;
    
    use Magento\CatalogInventory\Model\Stock\Item;
    use Magento\Catalog\Model\ProductFactory;
    
    class Data extends AbstractHelper
    {
        public function __construct(Context $context, ProductFactory $productFactory, Item $stockItem)
        {
            parent::__construct($context);
            $this->productFactory = $productFactory;
            $this->stockItem = $stockItem;
        }
        
        public function getChildProducts($_productId)
        {
            $outOfStockProducts = array();
            try {
                $configProduct = $this->productFactory->create()->load($_productId);
                $childProducts = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
                foreach ($childProducts as $childProduct) {
                    $stockItem = $this->getStockItem($childProduct->getID());
                    if ( !$stockItem->getQty() ) {
                        $outOfStockProducts[$childProduct->getID()] = $childProduct->getName();
                    }
                }
 
            } catch (\Exception $e) {
                return $e->getMassage();
            }
            return $outOfStockProducts;
        }
        public function getStockItem($productId)
        {
            $stockItem = $this->stockItem->load($productId, 'product_id');
            return $stockItem;
        }
    }