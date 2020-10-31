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
namespace Bss\CatalogPermission\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Bss\CatalogPermission\Model\Category;

/**
 * Class Data
 *
 * @package Bss\CatalogPermission\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Bss\CatalogPermission\Model\Category
     */
    protected $category;

    /**
     * Data constructor.
     * @param Context $context
     * @param Category $category
     */
    public function __construct(
        Context $context,
        Category $category
    ) {
        $this->category = $category;
        parent::__construct($context);
    }

    /**
     * Helper get function
     *
     * @param int $customerGroupId
     * @param int $currentStoreId
     * @param bool $isProductPermission
     * @return array
     */
    public function getIdCategoryByCustomerGroupId($customerGroupId, $currentStoreId, $isProductPermission = false, $collection)
    {
        return $this->category
            ->getListIdCategoryByCustomerGroupId($customerGroupId, $currentStoreId, $isProductPermission, $collection);
    }

    /**
     * Helper get function
     *
     * @param int $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIdCategoryByCustomerGroupIdDisableInCmsPage($customerGroupId)
    {
        return $this->category->getListIdCategoryByCustomerGroupIdDisableInCmsPage($customerGroupId);
    }

    /**
     * @return mixed
     */
    public function getCmsHomePage()
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $identifier
     * @return string
     */
    public function buildUrl($identifier)
    {
        return $this->_urlBuilder->getUrl(null, ['_direct' => $identifier]);
    }
}
