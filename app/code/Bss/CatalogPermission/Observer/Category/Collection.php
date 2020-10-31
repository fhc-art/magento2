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
namespace Bss\CatalogPermission\Observer\Category;

/**
 * Class Collection
 *
 * @package Bss\CatalogPermission\Observer\Category
 */
class Collection implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Bss\CatalogPermission\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Collection constructor.
     * @param \Bss\CatalogPermission\Helper\ModuleConfig $moduleConfig
     * @param \Bss\CatalogPermission\Helper\Data $helperData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Bss\CatalogPermission\Helper\ModuleConfig $moduleConfig,
        \Bss\CatalogPermission\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * Pre-filter Collection by category Id
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enableCatalogPermission = $this->moduleConfig->enableCatalogPermission();
        if ($enableCatalogPermission) {
            $customerGroupId = $this->customerSession->getCustomerGroupId();
            $currentStoreId = $this->storeManager->getStore()->getId();
            $listBannedId = array_unique(
                $this->helperData
                    ->getIdCategoryByCustomerGroupId($customerGroupId, $currentStoreId, false, true)
            );

            $productCollection = $observer->getEvent()->getCollection();
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection->addCategoriesFilter(['nin' => $listBannedId]);
            $observer->getEvent()->setCollection($productCollection);
        }
    }
}
