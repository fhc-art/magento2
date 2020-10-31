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
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Helper;

/**
 * Class Data
 *
 * @package Bss\ConfigurableProductWholesale\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIGURABLE_PRODUCT_TYPE = 'configurable';
    const DEFAULT_FINAL_PRICE_TEMPLATE = 'Magento_ConfigurableProduct::product/price/final_price.phtml';
    const CUSTOM_FINAL_PRICE_TEMPLATE = 'Bss_ConfigurableProductWholesale::product/price/final_price.phtml';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    private $localeFormat;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Locale\Currency
     */
    private $currencyLocale;

    /**
     * @var \Magento\Framework\Filter\LocalizedToNormalized
     */
    private $localFilter;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver;

    /**
     * @param \Magento\Framework\Filter\LocalizedToNormalized $localFilter
     * @param \Magento\Framework\Locale\ResolverInterfaceResolverInterface $localeResolver
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Locale\Currency $currencyLocale
     * @param \Magento\Framework\App\Helper\Context $context
     * @param MagentoHelper $magentoHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Filter\LocalizedToNormalized $localFilter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Locale\Currency $currencyLocale,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Registry $registry,
        MagentoHelper $magentoHelper
    ) {
        parent::__construct($context);
        $this->localeFormat = $localeFormat;
        $this->registry = $registry;
        $this->productMetadata = $productMetadata;
        $this->currencyLocale = $currencyLocale;
        $this->magentoHelper = $magentoHelper;
        $this->localFilter = $localFilter;
        $this->localeResolver = $localeResolver;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return MagentoHelper
     */
    public function getMagentoHelper()
    {
        return $this->magentoHelper;
    }

    /**
     * @return \Magento\Framework\Filter\LocalizedToNormalized
     */
    public function getLocalFilter()
    {
        return $this->localFilter;
    }

    /**
     * @return \Magento\Framework\Locale\ResolverInterface|\Magento\Framework\Locale\ResolverInterfaceResolverInterface
     */
    public function getLocaleResolver()
    {
        return $this->localeResolver;
    }

    /**
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }

    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * Get Configuration by Field
     *
     * @param string $field
     * @return mixed
     */
    public function getConfig($field = 'active')
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $active = $this->scopeConfig->getValue(
            'configurableproductwholesale/general/active',
            $scope
        );
        if (!$active || !$this->checkCustomer('active_customer_groups')) {
            return false;
        }
        $result = $this->scopeConfig->getValue(
            'configurableproductwholesale/general/' . $field,
            $scope
        );
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Get Show Out Of Stock Config Default
     *
     * @return mixed
     */
    public function getDisplayOutOfStock()
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            'cataloginventory/options/show_out_of_stock',
            $scope
        );
    }

    /**
     * Check attribute display
     *
     * @param string|null $value
     * @return bool
     */
    public function hasDisplayAttribute($value = null)
    {
        if (!$this->getConfig()) {
            return false;
        }
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $result = $this->scopeConfig->getValue(
            'configurableproductwholesale/general/show_attr',
            $scope
        );
        $resultArr = explode(',', $result);
        return in_array($value, $resultArr);
    }

    /**
     * @return string
     */
    public function getFomatPrice()
    {
        $config = $this->localeFormat->getPriceFormat();
        return $this->jsonHelper->jsonEncode($config);
    }

    /**
     * @param string|null $field
     * @return bool
     */
    public function checkCustomer($field = null)
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $customerConfig = $this->scopeConfig->getValue(
            'configurableproductwholesale/general/' . $field,
            $scope
        );
        if ($customerConfig != '') {
            $customerConfigArr = explode(',', $customerConfig);
            if ($this->magentoHelper->getCustomerSession()->create()->isLoggedIn()) {
                $customerGroupId = $this->magentoHelper->getCustomerSession()->create()->getCustomerGroupId();
                if (in_array($customerGroupId, $customerConfigArr)) {
                    return true;
                }
            } else {
                if (in_array(0, $customerConfigArr)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product|null $product
     * @return bool
     */
    public function checkTierPrice($product = null)
    {
        $storeId = $this->magentoHelper->getStoreId();
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($storeId, $product);
        $usedProducts = $productTypeInstance->getUsedProducts($product);
        $check = [];
        $count = 0;
        $apply = true;
        $countList = 0;
        foreach ($usedProducts as $child) {
            $tierPriceModel = $child->getPriceInfo()->getPrice('tier_price');
            $tierPricesList = $tierPriceModel->getTierPriceList();
            if (isset($tierPricesList)) {
                $countPricesList = $this->countTierPrice($tierPricesList);
                foreach ($tierPricesList as $price) {
                    if ($count == 0) {
                        $countList = $countPricesList;
                        $check[$price['price_qty']] = $price['website_price'];
                    } else {
                        $websitePrice = $price['website_price'];
                        if ($check && isset($check[$price['price_qty']]) &&
                            $check[$price['price_qty']] != $websitePrice ||
                            $countPricesList != $countList
                        ) {
                            $apply = false;
                            break;
                        }
                    }
                }
            } else {
                $apply = false;
                break;
            }
            $count++;
        }
        return $apply;
    }

    /**
     * @param array $tierPricesList
     * @return int
     */
    private function countTierPrice($tierPricesList)
    {
        return count($tierPricesList);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return mixed
     */
    public function setPriceForItem($item)
    {
        if (!isset($item)) {
            return;
        }
        $product = $item->getProduct();
        if (!$this->checkTierPrice($product)) {
            return;
        }
        $qty = $this->getTotalQty($item);
        foreach ($item->getQuote()->getAllVisibleItems() as $quoteItem) {
            $productId = $quoteItem->getProduct()->getId();
            $productType = $quoteItem->getProduct()->getTypeId();

            if ($productType != 'configurable' || $product->getId() != $productId) {
                continue;
            }

            $finalPrice = $quoteItem->getProduct()->getFinalPrice($qty);
            $quoteItem->setCustomPrice($finalPrice);
            $quoteItem->setOriginalCustomPrice($finalPrice);
            $quoteItem->getProduct()->setIsSuperMode(true);
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return mixed
     */
    public function getTotalQty($item)
    {
        $totalsQty = 0;
        if (!isset($item)) {
            return false;
        }
        $product = $item->getProduct();
        foreach ($item->getQuote()->getAllVisibleItems() as $quoteItem) {
            $productId = $quoteItem->getProduct()->getId();
            $productType = $quoteItem->getProduct()->getTypeId();
            if ($productType != 'configurable' || $product->getId() != $productId) {
                continue;
            }
            $totalsQty += $quoteItem->getQty();
        }
        if ($totalsQty > 0) {
            return $totalsQty;
        } else {
            return false;
        }
    }

    /**
     *  Get price template
     *
     * @return string
     */
    public function getPriceTemplate()
    {
        $product = $this->registry->registry('current_product');
        if (isset($product) && $this->getConfig('range_price') &&
            $product->getTypeId() == self::CONFIGURABLE_PRODUCT_TYPE
        ) {
            return self::CUSTOM_FINAL_PRICE_TEMPLATE;
        }
        return self::DEFAULT_FINAL_PRICE_TEMPLATE;
    }

    /**
     * @param null $price
     * @return string
     * @throws \Zend_Currency_Exception
     */
    public function getFormatPrice($price = null)
    {
        $currencyCode = $this->magentoHelper->getCurrencyCode();
        return $this->currencyLocale->getCurrency($currencyCode)->toCurrency($price);
    }

    /**
     * @param \Magento\Catalog\Model\Product|null $product
     * @param float|null $min
     * @param float|null $max
     * @return array|bool
     */
    public function getRangePrice($product = null, $min = null, $max = null)
    {
        $usedProducts = $product->getTypeInstance()->getUsedProducts($product);
        $price = [];
        $result = [];
        foreach ($usedProducts as $productChild) {
            $priceModel = $productChild->getPriceInfo()->getPrice('final_price');
            $productSku = $productChild->getSku();
            $stockStatus = $this->magentoHelper->getStockRegistry()->getProductStockStatusBySku($productSku);
            if ($stockStatus == 1) {
                $price['finalPrice'][] = $priceModel->getAmount()->getValue();
                $price['exclTaxFinalPrice'][] = $priceModel->getAmount()->getValue(['tax']);
                $tierPriceModel = $productChild->getPriceInfo()->getPrice('tier_price');
                $tierPricesList = $tierPriceModel->getTierPriceList();
                if (isset($tierPricesList) && !empty($tierPricesList)) {
                    foreach ($tierPricesList as $tierPrices) {
                        $price['finalPrice'][] = $tierPrices['price']->getValue();
                        $price['exclTaxFinalPrice'][] = $tierPrices['price']->getValue(['tax']);
                    }
                }
            }
        }

        $result['finalPrice'] = array_unique($price['finalPrice']);
        $result['exclTaxFinalPrice'] = array_unique($price['exclTaxFinalPrice']);
        $maxFinalPrice = max($result['finalPrice']);
        $maxExclTaxFinalPrice = max($result['exclTaxFinalPrice']);
        $minFinalPrice = min($result['finalPrice']);
        $minExclTaxFinalPrice = min($result['exclTaxFinalPrice']);
        if (isset($max)) {
            return [
                'finalPrice' => $maxFinalPrice,
                'exclTaxFinalPrice' => $maxExclTaxFinalPrice
            ];
        } elseif (isset($min)) {
            return [
                'finalPrice' => $minFinalPrice,
                'exclTaxFinalPrice' => $minExclTaxFinalPrice
            ];
        } else {
            return false;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product|null $product
     * @return string|bool
     */
    public function getJsonSystemConfig($product = null)
    {
        if (!$product) {
            return false;
        }
        $showSubTotal = $this->hasDisplayAttribute('subtotal') && !$this->checkCustomer('hide_price');
        $showExclTaxSubTotal = $this->hasExclTaxConfig() && !$this->checkCustomer('hide_price');
        $tierPriceAdvanced = $this->getConfig('tier_price_advanced') && $this->checkTierPrice($product);
        $ajaxConfig = $this->isAjax($product);
        $config = [
            'tierPriceAdvanced' => $tierPriceAdvanced,
            'showSubTotal' => $showSubTotal,
            'showExclTaxSubTotal' => $showExclTaxSubTotal,
            'textColor' => $this->getConfig('header_text_color'),
            'backGround' => $this->getConfig('header_background_color'),
            'ajaxLoad' => $ajaxConfig
        ];
        if ($this->getConfig('mobile_active')) {
            $config['mobile'] = $this->getDisplayAttributeAdvanced('mobile_attr', 'mobile_active');
        }
        if ($this->getConfig('tab_active')) {
            $config['tablet'] = $this->getDisplayAttributeAdvanced('tab_attr', 'tab_active');
        }
        $config['ajaxLoadUrl'] = $this->_urlBuilder->getUrl('configurablewholesale/index/rendertable');
        return $this->jsonHelper->jsonEncode($config);
    }

    /**
     * @param string $field
     * @param string|null $active
     * @return array|bool
     */
    public function getDisplayAttributeAdvanced($field, $active = null)
    {
        if (!$this->getConfig($active)) {
            return false;
        }
        $respon = [];
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $result = $this->scopeConfig->getValue(
            'configurableproductwholesale/general/' . $field,
            $scope
        );
        $resultArr = explode(',', $result);
        foreach ($resultArr as $value) {
            $respon[$value] = $value;
        }
        return $respon;
    }

    /**
     *  Add class for mobile and tablet
     *
     * @param string $value
     * @return string
     */
    public function getClassAdvanced($value)
    {
        $html = '';
        $mobileArr = $this->getDisplayAttributeAdvanced('mobile_attr', 'mobile_active');
        $tabletArr = $this->getDisplayAttributeAdvanced('tab_attr', 'tab_active');
        if (!empty($mobileArr) || !empty($tabletArr)) {
            $html .= 'class="';
            if (is_array($mobileArr) && !in_array($value, $mobileArr)) {
                $html .= 'bss-hidden-480';
            }
            if (is_array($tabletArr) && !in_array($value, $tabletArr)) {
                $html .= ' bss-hidden-1024';
            }
            $html .= '"';
        }
        return $html;
    }

    /**
     *  Compare magento version
     *
     * @param string $version
     * @return bool
     */
    public function validateMagentoVersion($version)
    {
        $dataVersion = $this->productMetadata->getVersion();
        if (version_compare($dataVersion, $version) >= 0) {
            return true;
        }
        return false;
    }

    /**
     * Check config exclude tax price
     *
     * @return bool
     */
    public function hasExclTaxConfig()
    {
        if ($this->getConfig() && $this->magentoHelper->getTaxHelper()->displayBothPrices() &&
            $this->hasDisplayAttribute('excl_tax_price')
        ) {
            return true;
        }
        return false;
    }

    public function isAjax($product)
    {
        $ajaxConfig = $product->getBssCpwAjax();
        if ($ajaxConfig == 2 || $ajaxConfig === null) {
            $ajaxConfig = $this->getConfig('ajax_load');
        }
        return $ajaxConfig;
    }
}
