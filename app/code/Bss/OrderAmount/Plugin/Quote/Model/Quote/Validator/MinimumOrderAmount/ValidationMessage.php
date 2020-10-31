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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_OrderAmount
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\OrderAmount\Plugin\Quote\Model\Quote\Validator\MinimumOrderAmount;

/**
 * Class ValidationMessage
 *
 * @package Bss\OrderAmount\Plugin\Quote\Model\Quote\Validator\MinimumOrderAmount
 */
class ValidationMessage
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $currency;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $helper;

    /**
     * ValidationMessage constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface $currency
     * @param \Bss\OrderAmount\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        \Bss\OrderAmount\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->currency = $currency;
        $this->helper = $helper;
    }

    /**
     * @param $subject
     * @param $proceed
     * @return \Magento\Framework\Phrase|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetMessage($subject, $proceed)
    {
        $message = $this->helper->getMessage();

        if (empty($message)) {
            $minimumAmount = $this->helper->getAmoutDataForCustomerGroup();
            if (!$minimumAmount) {
                $minimumAmount = 0;
            }

            $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $minimumAmount = $this->currency->getCurrency($currencyCode)->toCurrency($minimumAmount);
            $message = __('Minimum order amount is %1', $minimumAmount);
        } else {
            $message = __($message);
        }

        return $message;
    }
}
