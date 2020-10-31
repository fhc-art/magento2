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
namespace Bss\OrderAmount\Plugin\Quote\Model\Quote;

/**
 * Class Address
 *
 * @package Bss\OrderAmount\Plugin\Quote\Model\Quote
 */
class Address
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Bss\OrderAmount\Helper\Data
     */
    protected $helper;

    /**
     * Address constructor.
     *
     * @param \Bss\OrderAmount\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Bss\OrderAmount\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $subject
     * @param $proceed
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidateMinimumAmount($subject, $proceed)
    {
        $storeId = $subject->getQuote()->getStoreId();
        $validateEnabled = $this->scopeConfig->isSetFlag(
            'sales/minimum_order/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (!$validateEnabled) {
            return true;
        }

        if ((!$subject->getQuote()->getIsVirtual() xor
            $subject->getAddressType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING)) {
            return true;
        }

        $minAmount = $this->helper->getAmoutDataForCustomerGroup();
        if (!$minAmount) {
            return true;
        }

        $taxInclude = $this->scopeConfig->getValue(
            'sales/minimum_order/tax_including',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $taxes = $taxInclude ? $subject->getBaseTaxAmount() : 0;

        return ($subject->getBaseSubtotalWithDiscount() + $taxes >= $minAmount);
    }
}
