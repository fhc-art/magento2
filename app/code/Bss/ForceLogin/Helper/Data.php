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
 * @package    Bss_ForceLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ForceLogin\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * CookieMetadataFactory
     * @var CookieMetadataFactory $cookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * PhpCookieManager
     * @var PhpCookieManager $cookieMetadataManager
     */
    protected $cookieMetadataManager;

    /**
     * @var CatalogSession
     */
    protected $catalogSession;

    /**
     * Data constructor.
     * @param CatalogSession $catalogSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param PhpCookieManager $cookieMetadataManager
     * @param Context $context
     */
    public function __construct(
        CatalogSession $catalogSession,
        CookieMetadataFactory $cookieMetadataFactory,
        PhpCookieManager $cookieMetadataManager,
        Context $context
    ) {
        $this->catalogSession = $catalogSession;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieMetadataManager = $cookieMetadataManager;
        parent::__construct($context);
    }

    /**
     * Enable module
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for product page
     * @return bool
     */
    public function isEnableProductPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/product_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for category page
     * @return bool
     */
    public function isEnableCategoryPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/category_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for cart page
     * @return bool
     */
    public function isEnableCartPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/cart_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for checkout page
     * @return bool
     */
    public function isEnableCheckoutPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/checkout_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for contact page
     * @return bool
     */
    public function isEnableContactPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/contact_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for search term page
     * @return bool
     */
    public function isEnableSearchTermPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/search_term_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for search result page
     * @return bool
     */
    public function isEnableSearchResultPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/search_resulls_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable force login for advanced search page
     * @return bool
     */
    public function isEnableAdvancedSearchPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/advanced_search_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isEnableOtherPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/other_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable customer register
     * @return bool
     */
    public function isEnableRegister()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/general/disable_register',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get alert message after redirect login page
     * @return string
     */
    public function getAlertMessage()
    {
        $alertMessage = $this->scopeConfig->getValue(
            'forcelogin/page/message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $alertMessage;
    }

    /**
     * Get redirect url after login
     * @return string
     */
    public function getRedirectUrl()
    {
        $pageRedirect = $this->scopeConfig->getValue(
            'forcelogin/redirect_url/page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $pageRedirect;
    }

    /**
     * Get customer url after login
     * @return string
     */
    public function getCustomUrl()
    {
        $pageRedirect = $this->scopeConfig->getValue(
            'forcelogin/redirect_url/custom_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $pageRedirect;
    }

    /**
     * Enable force login for cms page
     * @return bool
     */
    public function isEnableCmsPage()
    {
        return $this->scopeConfig->isSetFlag(
            'forcelogin/page/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get cms Page id
     * @return string
     */
    public function getCmsPageId()
    {
        $cmsPageId = $this->scopeConfig->getValue(
            'forcelogin/page/page_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $cmsPageId;
    }

    /**
     * Get Redirect config default
     * @return bool
     */
    public function isRedirectDashBoard()
    {
        $redirectToDashBoard = $this->scopeConfig->isSetFlag(
            'customer/startup/redirect_dashboard',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $redirectToDashBoard;
    }

    /**
     * @return CatalogSession
     */
    public function getSessionCatalog()
    {
        return $this->catalogSession;
    }

    /**
     * Get Cms Index Page Id
     * @param string $pathPage
     * @return mixed
     */
    public function getCmsPageConfig($pathPage)
    {
        return $this->scopeConfig->getValue(
            $pathPage,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    public function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * @return CookieMetadataFactory|mixed
     */
    public function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }
}
