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

use Bss\CatalogPermission\Helper\Data;
use Bss\CatalogPermission\Helper\ModuleConfig;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Catalog
 *
 * @package Bss\CatalogPermission\Observer\Category
 */
class Catalog implements ObserverInterface
{
    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $response;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Bss\CatalogPermission\Helper\Data
     */
    protected $helperData;

    /**
     * Catalog constructor.
     * @param \Bss\CatalogPermission\Helper\ModuleConfig $moduleConfig
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Bss\CatalogPermission\Helper\Data $helperData
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        Http $response,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        Data $helperData,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->response = $response;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->helperData = $helperData;
    }

    /**
     * Observer execute
     *
     * @param Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $enableCatalogPermission = $this->moduleConfig->enableCatalogPermission();
        if (!$enableCatalogPermission) {
            return $this;
        }
        $categoryId = $observer->getRequest()->getParams();
        $categoryId = $categoryId['id'];
        $currentStoreId = $this->storeManager->getStore()->getId();
        $data = $this->categoryRepository->get($categoryId, $currentStoreId);
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $listIdSubCategory = $this->helperData->getIdCategoryByCustomerGroupId($customerGroupId, $currentStoreId, false, false);
        $useParentCategory = $this->moduleConfig->useParentCategory();
        if ($useParentCategory && in_array($categoryId, $listIdSubCategory)) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $this->response->setRedirect($baseUrl . 'catalogpermission/index/index?pagetype=category');
        }

        if (isset($data['bss_customer_group'])) {
            if ($data['bss_customer_group'] !== null) {
                if ($data['bss_customer_group'] == $customerGroupId || (is_array($data['bss_customer_group'])
                        && in_array($customerGroupId, $data['bss_customer_group']))) {
                    $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                    $this->response->setRedirect($baseUrl . 'catalogpermission/index/index?pagetype=category');
                }
            }
        }
        return $this;
    }
}
