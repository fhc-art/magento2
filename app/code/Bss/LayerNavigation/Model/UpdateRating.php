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
namespace Bss\LayerNavigation\Model;

class UpdateRating
{
    /**
     * UpdateRating constructor.
     * @param \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory $summaryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\Action $productAction
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory $summaryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productAction
    ) {
        $this->summaryCollectionFactory = $summaryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productAction = $productAction;
    }

    /**
     *
     */
    public function apply()
    {
        $storeIds = array_keys($this->storeManager->getStores());
        foreach ($storeIds as $storeId) {
            $collection = $this->loadCollection($storeId);
            foreach ($collection as $review) {
                $rating = round($review->getData('rating_summary') / 20);
                $updateAttributes = ['rating' => $rating];
                $productIds = [$review->getData('entity_pk_value')];
                $this->productAction->updateAttributes($productIds, $updateAttributes, $storeId);
            }
        }
    }

    /**
     * @param $storeId
     * @return mixed
     */
    private function loadCollection($storeId)
    {
        return $this->summaryCollectionFactory->create()->addStoreFilter($storeId);
    }
}
