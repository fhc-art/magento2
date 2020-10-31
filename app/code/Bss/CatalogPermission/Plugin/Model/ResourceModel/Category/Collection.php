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
namespace Bss\CatalogPermission\Plugin\Model\ResourceModel\Category;

use Bss\CatalogPermission\Helper\Data;
use Bss\CatalogPermission\Helper\ModuleConfig;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Collection
 *
 * @package Bss\CatalogPermission\Plugin\Model\ResourceModel\Category
 */
class Collection
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Bss\CatalogPermission\Helper\Data
     */
    protected $helperData;
    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Collection constructor.
     * @param Session $customerSession
     * @param ModuleConfig $moduleConfig
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Session $customerSession,
        ModuleConfig $moduleConfig,
        Data $helperData,
        StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
    }

    /**
     * Plugin before
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLoad($subject, $printQuery = false, $logQuery = false)
    {
        $disableCategoryLink = $this->moduleConfig->disableCategoryLink();
        $enableCatalogPermission = $this->moduleConfig->enableCatalogPermission();

        if ($disableCategoryLink && $enableCatalogPermission) {
            $customerGroupId = $this->customerSession->getCustomerGroupId();
            $currentStoreId = $this->storeManager->getStore()->getId();
            $arrIds = $this->helperData->getIdCategoryByCustomerGroupId($customerGroupId, $currentStoreId, false, false);
            if ($arrIds) {
                $subject->addAttributeToFilter('entity_id', ['nin' => $arrIds]);
            }
        }
    }
}
