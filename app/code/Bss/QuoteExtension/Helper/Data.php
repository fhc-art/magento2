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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\QuoteExtension\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Bss\QuoteExtension\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PRODUCT_CONFIG_ENABLE = 1;
    const PRODUCT_CONFIG_DISABLE = 2;
    const PATH_REQUEST4QUOTE_ENABLED = 'bss_request4quote/general/enable';
    const PATH_REQUEST4QUOTE_ICON = 'bss_request4quote/request4quote_global/icon';
    const PATH_REQUEST4QUOTE_VALIDATE_QTY = 'bss_request4quote/request4quote_global/validate_qty_product';
    const PATH_REQUEST4QUOTE_QUOTABLE = 'bss_request4quote/request4quote_global/quotable';
    const PATH_REQUEST4QUOTE_APPLY_CUSTOMER = 'bss_request4quote/request4quote_global/customers';
    const PATH_REQUEST4QUOTE_DISABLE_RESUBMIT = 'bss_request4quote/request4quote_global/disable_resubmit';
    const PATH_REQUEST4QUOTE_ITEMS_COMMENT = 'bss_request4quote/request4quote_global/disable_items_comment';
    const PATH_REQUEST4QUOTE_SHIPPING_REQUIRED = 'bss_request4quote/request4quote_global/shipping_required';

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Bss\QuoteExtension\Model\Config\Source\Status
     */
    protected $status;

    /**
     * @var View
     */
    protected $customerHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var ConfigRequestButton
     */
    protected $helperConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param View $customerHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param TimezoneInterface $localeDate
     * @param ConfigRequestButton $helperConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        View $customerHelper,
        CustomerRepositoryInterface $customerRepository,
        TimezoneInterface $localeDate,
        ConfigRequestButton $helperConfig
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManagerInterface;
        $this->currencyFactory = $currencyFactory;
        $this->priceCurrency = $priceCurrency;
        $this->pricingHelper = $pricingHelper;
        $this->customerHelper = $customerHelper;
        $this->customerRepository = $customerRepository;
        $this->localeDate = $localeDate;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Is Enable Module
     *
     * @param int $store
     * @return bool
     */
    public function isEnable($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_REQUEST4QUOTE_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Customer Group Id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->helperConfig->getCustomerGroupId();
    }

    /**
     * Check Resubmit Quote Enable
     *
     * @param int $store
     * @return bool
     */
    public function disableResubmit($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_REQUEST4QUOTE_DISABLE_RESUBMIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get quoteable
     *
     * @param int $store
     * @return int
     */
    public function getQuotable($store = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_QUOTABLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Apply For Customer Group
     *
     * @param int $store
     * @return array
     */
    public function getApplyForCustomers($store = null)
    {
        return $this->toArray($this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_APPLY_CUSTOMER,
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * Is Enable Quote Item Comment
     *
     * @param int $store
     * @return array
     */
    public function isEnableQuoteItemsComment($store = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_ITEMS_COMMENT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check Apply Default Quantity Condition When Adding Product to Quote
     *
     * @param int $store
     * @return bool
     */
    public function validateQuantity($store = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_VALIDATE_QTY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Icon For Request Quote
     *
     * @param int $store
     * @return \Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIcon($store = null)
    {
        $image = '';
        $pointImage = $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_ICON,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($pointImage) {
            $imageSrc = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'bss/request4quote/' . $pointImage;
            $image = __('<img src="%1" alt="request4quote-icon"/>', $imageSrc);
        }
        return $image;
    }

    /**
     * Get Current Date
     *
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->localeDate->date()->format('Y-m-d');
    }

    /**
     * Get Current Date Time
     *
     * @return string
     */
    public function getCurrentDateTime()
    {
        return $this->localeDate->date()->format('Y-m-d H:i:s');
    }

    /**
     * Is active Request4quote for product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $storeId
     * @return bool
     */
    public function isActiveRequest4Quote($product, $storeId = null)
    {
        if ($this->isEnable($storeId)) {
            $customerGroup = $this->getCustomerGroupId();
            return $this->isActiveForProduct($product, $storeId, $customerGroup);
        }
        return false;
    }

    /**
     * Is active Request4quote for Product Config
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $storeId
     * @param int $customerGroup
     * @return bool
     */
    protected function isActiveForProduct($product, $storeId, $customerGroup)
    {
        if ($product->getBssRequestQuote() == self::PRODUCT_CONFIG_DISABLE) {
            return false;
        } elseif ($product->getBssRequestQuote() == self::PRODUCT_CONFIG_ENABLE) {
            return $this->isActiveProductConfig($product, $customerGroup);
        }
        return $this->isActiveForCategory($product, $storeId, $customerGroup);
    }

    /**
     * Is active Request4quote for Category Config
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $storeId
     * @param int $customerGroup
     * @return bool
     * @throws \Zend_Db_Statement_Exception
     */
    protected function isActiveForCategory($product, $storeId, $customerGroup)
    {
        $categories = $product->getCategoryIds();
        $categoryCheck = $this->helperConfig->getConfigButtonCategory($categories, $storeId, $customerGroup);
        if ($categoryCheck === 'enable') { //enable in category
            return true;
        } elseif ($categoryCheck === 'disable') { //disable in category
            return false;
        } else { //use global config
            return $this->isActiveGlobalConfig();
        }
    }

    /**
     * Is active Product Config
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $customerGroup
     * @return bool
     */
    private function isActiveProductConfig($product, $customerGroup)
    {
        if ($product->getQuoteCusGroup() == '') {
            return false;
        }
        $productConfigCustomerGroup = explode(',', $product->getQuoteCusGroup());
        if (in_array($customerGroup, $productConfigCustomerGroup)) {
            return true;
        }
        return false;
    }

    /**
     * Is active Global Config
     *
     * @return bool
     */
    private function isActiveGlobalConfig()
    {
        if ($this->getQuotable()) {
            if ($this->getQuotable() == 2) {
                $customerGroup = $this->getCustomerGroupId();
                if (in_array($customerGroup, $this->getApplyForCustomers())) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * To array function
     *
     * @param string $string
     * @return array
     */
    public function toArray($string)
    {
        $string = str_replace(' ', '', $string);
        $array = explode(',', $string);
        $newArray = array_filter($array, function ($value) {
            return $value !== '';
        });
        return $newArray;
    }

    /**
     * Get Currency Symbol
     *
     * @param int $store
     * @return string
     */
    public function getCurrentCurrencySymbol($store = null)
    {
        if ($store) {
            $currency = $this->currencyFactory->create()->load($store->getCurrentCurrencyCode());
            return $currency->getCurrencySymbol();
        } else {
            return $this->priceCurrency->getCurrency()->getCurrencySymbol();
        }
    }

    /**
     * Generate new random token
     *
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Retrieve format price
     *
     * @param $value
     * @param bool $storeId
     * @return float
     */
    public function formatPrice($value, $storeId = false, $currency = false)
    {
        return $this->priceCurrency->format(
            $value,
            true,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            $storeId,
            $currency
        );
    }

    /**
     * Retrieve formated price include symbol
     *
     * @param float $value
     * @return float|string
     */
    public function formatCurrencyIncludeSymbol($value)
    {
        return $this->pricingHelper->currency($value, true, false);
    }

    /**
     * Retrieve formated price exclude symbol
     *
     * @param float $value
     * @return float|string
     */
    public function formatCurrencyExcludeSymbol($value)
    {
        return $this->pricingHelper->currency($value, false, false);
    }

    /**
     * Get customer name
     *
     * @param int $customerId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerName($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        return $this->customerHelper->getCustomerName($customer);
    }

    /**
     * Get Customer By Id
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerById($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Return Required Address Config
     *
     * @param int $store
     * @return bool
     */
    public function isRequiredAddress($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_REQUEST4QUOTE_SHIPPING_REQUIRED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return Pending Request quote
     *
     * @return string
     */
    public function returnPendingStatus()
    {
        return \Bss\QuoteExtension\Model\Config\Source\Status::STATE_PENDING;
    }
}
