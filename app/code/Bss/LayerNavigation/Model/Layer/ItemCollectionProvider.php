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

namespace Bss\LayerNavigation\Model\Layer;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Bss\LayerNavigation\Model\ResourceModel\Fulltext\CollectionFactory;

/**
 * Class ItemCollectionProvider
 * @package Bss\LayerNavigation\Model\Layer
 */
class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    private $rootCategoryCollection = null;
    private $_productCollections = [];

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {

        /** @var \Bss\LayerNavigation\Model\ResourceModel\Fulltext\Collection $collection */
        if ($category->getParentId() == 1) {
            if ($this->rootCategoryCollection) {
                return $this->rootCategoryCollection;
            }
            $collection = $this->collectionFactory->create(['searchRequestName' => 'quick_search_container']);
            $this->rootCategoryCollection = $collection;
        } else {
            if (isset($this->_productCollections[$category->getId()])) {
                $collection = $this->_productCollections[$category->getId()];
            } else {
                $collection = $this->collectionFactory->create();
                $collection->addCategoryFilter($category);
                if ($collection->getSize() > 0) {
                    $this->_productCollections[$category->getId()] = $collection;
                }
            }
        }
        return $collection;
    }
}
