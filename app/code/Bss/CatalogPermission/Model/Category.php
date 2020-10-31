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
 * @package    Bss_CatalogPermission
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CatalogPermission\Model;

use Bss\CatalogPermission\Helper\ModuleConfig;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Category
 *
 * @package Bss\CatalogPermission\Model
 */
class Category
{
    /**
     * @var ResourceModel\Category
     */
    protected $categoryResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Category constructor.
     * @param ResourceModel\Category $categoryResource
     * @param CollectionFactory $categoryCollectionFactory
     * @param ModuleConfig $moduleConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Category $category
     */
    public function __construct(
        ResourceModel\Category $categoryResource,
        CollectionFactory $categoryCollectionFactory,
        ModuleConfig $moduleConfig,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Category $category
    ) {
        $this->categoryResource = $categoryResource;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->category = $category;
        $this->moduleConfig = $moduleConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get all category id that disable in CMS page
     *
     * @param int $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListIdCategoryByCustomerGroupIdDisableInCmsPage($customerGroupId)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('bss_customer_group');
        $collection->setStore($this->storeManager->getStore());
        $collection->addAttributeToFilter('bss_customer_group', ['like' => "%{$customerGroupId}%"]);

        $useParentCategory = $this->moduleConfig->useParentCategory();
        $arrIds = $collection->getAllIds();
        $data = $collection->getData();
        if ($useParentCategory) {
            $subIds = '';
            if ($arrIds) {
                foreach ($data as $categoryData) {
                    $subId = $this->getListIdSubCategoryByParentIdSQL($categoryData);
                    $subIds .= ',' . $subId;
                }
            }
            $subIds = substr($subIds, 1);
            $subIds = explode(',', $subIds);
            $arrIds = array_merge($arrIds, $subIds);
        }
        return $arrIds;
    }

    /**
     * Get all category disable by Customer Group Id
     *
     * @param int $customerGroupId
     * @param int $currentStoreId
     * @param bool $isProductPermission
     * @return array
     */
    public function getListIdCategoryByCustomerGroupId($customerGroupId, $currentStoreId, $isProductPermission = false, $collection)
    {
        $useParentCategory = $this->moduleConfig->useParentCategory();
        $collection = $this->categoryCollectionFactory->create();
        $subCollection = $this->categoryCollectionFactory->create();
        $collection = $this->categoryResource
            ->getCategoryCollectionWithBssAttribute($collection, $subCollection, $customerGroupId, $currentStoreId);
        $arrIds = $collection->getAllIds();
        $data = $collection->getData();
        if ($useParentCategory || $isProductPermission) {
            $subIds = '';
            if ($data) {
                foreach ($data as $categoryData) {
                    $subId = $this->getListIdSubCategoryByParentIdSQL($categoryData, $currentStoreId);
                    $subIds .= ',' . $subId;
                }
            }
            $subIds = substr($subIds, 1);
            $subIds = explode(',', $subIds);
            $arrIds = array_merge($arrIds, $subIds);
        }
        if (!$collection) {
            foreach ($this->getListCateIdsToUnset($arrIds, $data, $currentStoreId) as $key) {
                unset($arrIds[$key]);
            }
        }
        return $arrIds;
    }

    /**
     * Get list category ids to unset
     *
     * @param array $arrIds
     * @param array $data
     * @param int $currentStoreId
     * @return array
     */
    protected function getListCateIdsToUnset($arrIds, $data, $currentStoreId)
    {
        $idToUnsset = [];
        $categoryChildIds = $this->getCategoryChilds($arrIds, $data, $currentStoreId);
        foreach ($arrIds as $key => $id) {
            $isUnset = false;
            if (isset($categoryChildIds[$id])) {
                $tempArrIds = $arrIds;
                unset($tempArrIds[$key]);
                $hasChildIdPermission = 0;
                foreach ($tempArrIds as $tempId) {
                    if (in_array($tempId, $categoryChildIds[$id])) {
                        $hasChildIdPermission++;
                    }
                }

                if ($hasChildIdPermission < $this->getCountCategoryChild($categoryChildIds[$id])) {
                    $isUnset = true;
                }
            } else {
                $isUnset = false;
            }
            if ($isUnset) {
                $idToUnsset[$id] = $key;
            }
        }
        return $idToUnsset;
    }

    protected function getCountCategoryChild($childIdArr)
    {
        return count($childIdArr);
    }

    /**
     * Get All Child Ids of permission categories
     *
     * @param array $arrIds
     * @param array $data
     * @param int $currentStoreId
     * @return array
     */
    protected function getCategoryChilds($arrIds, $data, $currentStoreId)
    {
        $categoryChildIds = [];
        foreach ($arrIds as $categoryId) {
            foreach ($data as $categoryData) {
                if ($categoryId == $categoryData['entity_id']) {
                    $childIds = explode(',', $this->getListIdSubCategoryByParentIdSQL($categoryData, $currentStoreId));
                    $childIds = array_filter($childIds);
                    unset($childIds[0]);
                    if (!empty($childIds)) {
                        $categoryChildIds[$categoryId] = $childIds;
                    }
                }
            }
        }
        return $categoryChildIds;
    }

    /**
     * Get sub category Id by parent Id
     *
     * @param array $data
     * @param int $currentStoreId
     * @return array|string
     */
    public function getListIdSubCategoryByParentIdSQL($data, $currentStoreId = 0)
    {
        $subCategoryIds = $this->categoryResource->getChildrenIds($data, $currentStoreId);
        $subCategoryIds = implode(",", $subCategoryIds);
        $subCategoryIds = $data['entity_id'] . ',' . $subCategoryIds;
        return $subCategoryIds;
    }

    /**
     * Check if any string in array
     *
     * @param string $string
     * @param array $array
     * @return bool
     */
    public function inArrayByString($string, $array)
    {
        $arr = explode(',', $string);
        foreach ($arr as $value) {
            if ($value == "") {
                return true;
            }
        }
        if (!empty($arr)) {
            foreach ($arr as $val) {
                if (!in_array($val, $array)) {
                    return false;
                }
            }
        }
        return true;
    }
}
