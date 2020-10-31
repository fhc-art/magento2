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
 * @package    Bss_ReorderProduct
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ReorderProduct\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Class ReorderProduct
 *
 * @package Bss\ReorderProduct\Block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReorderProduct extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Bss\ReorderProduct\Helper\Data
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    protected $orders;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $orderItem;

    /**
     * @var \Bss\ReorderProduct\Model\ResourceModel\OrderItem\CollectionFactory
     */
    protected $orderItemCollection;

    /**
     * @var \Magento\CatalogInventory\Api\StockStatusRepositoryInterface
     */
    protected $stockStatusRepository;

    /**
     * @var \Bss\ReorderProduct\Helper\HelperClass
     */
    protected $helperClass;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;
    
    protected $item;

    /**
     * ReorderProduct constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\ReorderProduct\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItem
     * @param \Bss\ReorderProduct\Model\ResourceModel\OrderItem\CollectionFactory $orderItemCollection
     * @param \Magento\CatalogInventory\Api\StockStatusRepositoryInterface $stockStatusRepository
     * @param \Bss\ReorderProduct\Helper\HelperClass $helperClass
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\ReorderProduct\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItem,
        \Bss\ReorderProduct\Model\ResourceModel\OrderItem\CollectionFactory $orderItemCollection,
        \Magento\CatalogInventory\Api\StockStatusRepositoryInterface $stockStatusRepository,
        \Bss\ReorderProduct\Helper\HelperClass $helperClass,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        array $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
        $this->orderItem = $orderItem;
        $this->orderItemCollection = $orderItemCollection;
        $this->stockStatusRepository = $stockStatusRepository;
        $this->helperClass = $helperClass;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Reorder Product'));
    }

    /**
     * Get stock by product id
     *
     * @param int $productId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStock($productId)
    {
        $webid = $this->_storeManager->getStore()->getWebsiteId();
        $criteria = $this->helperClass->returnStockStatusCriteriaFactory()->create();
        $criteria->setProductsFilter($productId);
        $criteria->addFilter('website_id', 'website_id', 0);
        $result = $this->stockStatusRepository->getList($criteria);
        $stockStatus = current($result->getItems());
        if (!$stockStatus) {
            $stockStatus = $this->helperClass->returnStockRegistry()->getStockItem($productId, $webid);
        }
        return $stockStatus;
    }

    /**
     * Get min sale qty
     *
     * @param Item $item
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMinSaleQty($item)
    {
        $productId = $item->getProductId();
        if ($item->getProductType() == 'configurable' && $this->getChildProduct($item) != null) {
            $productId = $this->getChildProduct($item)->getId();
        }
        $webid = $this->_storeManager->getStore()->getWebsiteId();
        $stockItem = $this->helperClass->returnStockRegistry()->getStockItem($productId, $webid);
        return $stockItem->getMinSaleQty() ? $stockItem->getMinSaleQty() : 1;
    }

    /**
     * Get order collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }

    /**
     * Get Orders
     *
     * @return bool|array
     */
    public function getOrders()
    {
        if (!($customerId = $this->helperClass->returnCustomerSession()->create()->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
                'entity_id'
            )->addFieldToFilter(
                'status',
                ['in' => $this->helperClass->returnOrderConfig()->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        if ($this->orders->getSize()) {
            return $this->orders->getAllIds();
        }

        return false;
    }

    /**
     * Get media base url
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Truncate String
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string $remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]
        );
    }

    /**
     * Format option value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = '';
        $value = $this->truncateString($value, 55, '', $remainder);
        $result = ['value' => nl2br($value), 'remainder' => nl2br($remainder)];

        return $result;
    }

    /**
     * Avaiable orders config array
     *
     * @return array
     */
    public function getAvailableOrders()
    {
        $sort = [
            'name'=>'2',
            'price'=>'3',
            'qty_ordered'=>'5',
            'created_at'=>'6',
            'stock_status'=>'7'
        ];
        return $sort;
    }

    /**
     * Get default order config
     *
     * @return mixed
     */
    public function getOrderDefault()
    {
        $sortby = $this->getAvailableOrders();
        return $sortby[$this->helper->getSortby()];
    }

    /**
     * Get order items collection
     *
     * @return mixed
     */
    public function getItems()
    {
        $_orders = $this->getOrders();

        $collection  = $this->orderItemCollection->create();
        $collection->filterOrderIds($_orders);
        return $collection;
    }

    /**
     * Get show items per page value
     *
     * @return array
     */
    public function getListperpagevalue()
    {
        $item_per_page = array_combine(
            explode(',', $this->helper->getListperpagevalue()),
            explode(',', $this->helper->getListperpagevalue())
        );
        if ($this->helper->showAlllist()) {
            $item_per_page['-1'] = 'All';
        }
        return $item_per_page;
    }

    /**
     * Get show items per page
     *
     * @return mixed
     */
    public function getListperpage()
    {
        return $this->helper->getListperpage();
    }

    /**
     * Get product id in item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return mixed
     */
    public function getProductId($item)
    {
        $productId = $item->getProductId();
        $itemOptions = $this->helperClass->returnSerializer()->serialize($item->getReorderItemOptions());
        if ($item->getProductType() == 'configurable' && isset($itemOptions['product'])) {
            $productId = $itemOptions['product'];
        }
        if ($item->getProductType() == 'grouped' && isset($itemOptions['super_product_config']['product_id'])) {
            $productId = $itemOptions['super_product_config']['product_id'];
        }
        return $productId;
    }

    /**
     * Get child product
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return null
     */
    public function getChildProduct($item)
    {
        $product = null;
        if ($item->getProductType() == 'configurable') {
            $collection  = $this->orderItem->create()->getCollection();
            $collection->addFieldToFilter('parent_item_id', $item->getId());
            $collection->addAttributeToSelect('product_id');
            if ($collection->getSize() > 0) {
                foreach ($collection as $item) {
                    $product = $item->getProduct();
                    break;
                }
            }
        }
        return $product;
    }

    /**
     * Get helper Bss
     *
     * @return \Bss\ReorderProduct\Helper\Data
     */
    public function getBssHelperData()
    {
        return $this->helper;
    }

    /**
     * Format price by store
     *
     * @param float $amount
     * @param int $store
     * @return float|string
     */
    public function formatPrice($amount, $store)
    {
        return $this->priceCurrency->format(
            $amount,
            true,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            $store
        );
    }

    /**
     * Check can show button reorder product
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return bool
     */
    public function canShowButtonReorder($item)
    {
        $product = $item->getProduct();
        if ($product->getIsSalable()) {
            return true;
        }
        return false;
    }

    /**
     * Set Item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return \Magento\Sales\Model\Order\Item
     */
    public function setItem($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            $this->item = $item;
        }
        return $this->item;
    }

    /**
     * Get item
     *
     * @return \Magento\Sales\Model\Order\Item |mixed
     */
    public function getItem()
    {
        return $this->item;
    }
}
