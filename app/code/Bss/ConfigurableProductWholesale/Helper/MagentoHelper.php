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

class MagentoHelper
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $customerSession;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    private $cartHelper;
    
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    private $currency;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Directory\Model\PriceCurrency $currency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Tax\Helper\Data $taxHelper
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Directory\Model\PriceCurrency $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Tax\Helper\Data $taxHelper
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->cartHelper = $cartHelper;
        $this->customerSession = $customerSession;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
        $this->taxHelper = $taxHelper;
    }

    /**
     * @return \Magento\Checkout\Helper\Cart
     */
    public function getCartHelper()
    {
        return $this->cartHelper;
    }

    /**
     * @return \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public function getStockRegistry()
    {
        return $this->stockRegistry;
    }

    /**
     * @return \Magento\Customer\Model\SessionFactory
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency->getCurrency()->getCurrencyCode();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->taxHelper;
    }
}