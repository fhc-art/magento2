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
namespace Bss\OrderAmount\Plugin\Quote\Model;

/**
 * Class Quote
 *
 * @package Bss\OrderAmount\Plugin\Quote\Model
 */
class Quote
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
     * Quote constructor.
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
     * @param bool $multishipping
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidateMinimumAmount($subject, $proceed, $multishipping = false)
    {
        $storeId = $subject->getStoreId();
        $minOrderActive = $this->scopeConfig->isSetFlag(
            'sales/minimum_order/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$minOrderActive) {
            return true;
        }

        $minOrderMulti = $this->scopeConfig->isSetFlag(
            'sales/minimum_order/multi_address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $minAmount = $this->helper->getAmoutDataForCustomerGroup();
        if (!$minAmount) {
            return true;
        }

        $taxInclude = $this->scopeConfig->getValue(
            'sales/minimum_order/tax_including',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $addresses = $subject->getAllAddresses();

        if (!$multishipping) {
            foreach ($addresses as $address) {
                /* @var $address Address */
                if (!$address->validateMinimumAmount()) {
                    return false;
                }
            }
            return true;
        }

        if (!$this->hasOrderAmount($minOrderMulti, $addresses, $taxInclude, $minAmount)) {
            return false;
        }

        return true;
    }

    /**
     * @param $minOrderMulti
     * @param $addresses
     * @param $taxInclude
     * @param $minAmount
     * @return bool
     */
    protected function hasOrderAmount($minOrderMulti, $addresses, $taxInclude, $minAmount)
    {
        if (!$minOrderMulti) {
            foreach ($addresses as $address) {
                $taxes = ($taxInclude) ? $address->getBaseTaxAmount() : 0;
                $amount = 0;

                foreach ($address->getQuote()->getItemsCollection() as $item) {
                    /** @var \Magento\Quote\Model\Quote\Item $item */
                    $amount += $item->getBaseRowTotal() - $item->getBaseDiscountAmount() + $taxes;
                }

                if ($amount < $minAmount) {
                    return false;
                }
            }
        } else {
            $baseTotal = 0;
            foreach ($addresses as $address) {
                $taxes = ($taxInclude) ? $address->getBaseTaxAmount() : 0;
                $baseTotal += $address->getBaseSubtotalWithDiscount() + $taxes;
            }
            if ($baseTotal < $minAmount) {
                return false;
            }
        }
        return true;
    }
}
