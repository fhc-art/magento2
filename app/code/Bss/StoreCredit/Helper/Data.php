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
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Bss\StoreCredit\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $directoryFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $directoryFactory
     * @param Context $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrencyFactory $directoryFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->directoryFactory = $directoryFactory;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Convert Price from store price to base
     *
     * @param float|int $price
     * @return float
     */
    public function convertBaseFromCurrency($price = 0)
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $rateToBase = $this->directoryFactory->create()->load($currentCurrency)->getAnyRate($baseCurrency);
        $priceConverted = $price * $rateToBase;
        return $priceConverted;
    }

    /**
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     */
    public function getEmailConfig($field, $storeId = null)
    {
        $scope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('storecredit/email/' . $field, $scope, $storeId);
    }

    /**
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        if (!$this->scopeConfig->getValue('storecredit/general/active')) {
            return false;
        }
        $scope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('storecredit/general/' . $field, $scope, $storeId);
    }

    /**
     * Get action store credit
     *
     * @param int $value
     * @return string
     */
    public function getTypeAction($value)
    {
        $result = '';
        switch ($value) {
            case 1:
                $result = __('Refund');
                break;
            case 2:
                $result = __('Used in order');
                break;
            case 3:
                $result = __('Update');
                break;
        }
        return $result;
    }
}
