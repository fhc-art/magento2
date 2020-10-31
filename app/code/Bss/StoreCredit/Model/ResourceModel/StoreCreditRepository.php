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

namespace Bss\StoreCredit\Model\ResourceModel;

use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\SessionFactory as CheckoutSession;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Json\Helper\Data;
use Bss\StoreCredit\Helper\Data as StoreCreditData;
use Bss\StoreCredit\Model\CreditFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Helper\Data as PricingData;

/**
 * Class StoreCreditRepository
 * @package Bss\StoreCredit\Model\ResourceModel
 */
class StoreCreditRepository implements StoreCreditRepositoryInterface
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Bss\StoreCredit\Model\CreditFactory
     */
    private $creditFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $customerSession;

    /**
     * @var array
     */
    private $storeCreditRegistryByCustomer = [];

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @param StoreCreditData $bssStoreCreditHelper
     * @param CreditFactory $creditFactory
     * @param SessionFactory $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CheckoutSession $checkoutSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $jsonHelper
     * @param PricingData $priceHelper
     */
    public function __construct(
        StoreCreditData $bssStoreCreditHelper,
        CreditFactory $creditFactory,
        SessionFactory $customerSession,
        StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        Data $jsonHelper,
        PricingData $priceHelper
    ) {
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->creditFactory = $creditFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->jsonHelper = $jsonHelper;
        $this->priceCurrency = $priceCurrency;
        $this->priceHelper = $priceHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($amount)
    {
        if ($this->bssStoreCreditHelper->getGeneralConfig('checkout_page_display')) {
            $response = [];
            $amount = $this->priceCurrency->round($amount);
            $quote = $this->checkoutSession->create()->getQuote();
            $baseAmount = $this->priceCurrency->round($this->bssStoreCreditHelper->convertBaseFromCurrency($amount));
            $creditModel = $this->creditFactory->create();
            $totals_amount = $quote->getTotals()['grand_total']->getValue();
            if ($amount < 0 || !$quote->getId() || !$creditModel->validateBalance($quote, $baseAmount)) {
                $response['status'] = false;
                $response['message'] = __('Something went wrong. Please enter a value again');
            } elseif ($baseAmount > $totals_amount) {
                $response['status'] = false;
                $response['message'] = __('Make sure you don\'t apply store credit more than order total.');
            } else {
                $quote->setBaseBssStorecreditAmountInput($baseAmount);
                $quote->collectTotals();
                $quote->save();
                $amountLeft = $this->get()->getBalanceAmount() - $quote->getBssStorecreditAmount();
                $response['status'] = true;
                $response['message'] = __('Success.');
                $response['amount'] = $quote->getBssStorecreditAmount();
                $response['total'] = $this->priceHelper->currency($amountLeft, true, false);

            }
            return $this->jsonHelper->jsonEncode($response);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($customerId = null, $websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = (int) $this->storeManager->getStore()->getWebsiteId();
        }
        if ($customerId === null) {
            $customerId = (int) $this->customerSession->create()->getCustomer()->getId();
        }

        if (isset($this->storeCreditRegistryByCustomer[$customerId])) {
            return $this->storeCreditRegistryByCustomer[$customerId];
        }

        $creditModel = $this->creditFactory->create();

        if (isset($websiteId)) {
            $creditModel->setWebsiteId($websiteId);
        }

        $credit = $creditModel->loadByCustomer($customerId);
        return $credit;
    }
}
