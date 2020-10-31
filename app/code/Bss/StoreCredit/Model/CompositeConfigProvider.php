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

namespace Bss\StoreCredit\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\SessionFactory as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data;
use Bss\StoreCredit\Helper\Data as StoreCreditData;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class CompositeConfigProvider
 * @package Bss\StoreCredit\Model
 */
class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param Data $priceHelper
     * @param StoreCreditData $bssStoreCreditHelper
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Data $priceHelper,
        StoreCreditData $bssStoreCreditHelper,
        StoreCreditRepositoryInterface $storeCreditRepository,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output = [];
        if ($this->bssStoreCreditHelper->getGeneralConfig('checkout_page_display')) {
            $credit = $this->storeCreditRepository->get();
            $qoute = $this->checkoutSession->create()->getQuote();
            $balanceUsed = $qoute->getBssStorecreditAmount();
            $output['storeCreditQuote'] = '';
            $output['storeCreditTotal'] = '';
            if ($credit->getId()) {
                $amountLeft = $credit->getBalanceAmount() - $qoute->getBaseBssStorecreditAmount();
                $output['storeCreditQuote'] = $this->priceCurrency->round($balanceUsed);
                $output['storeCreditTotal'] = $this->priceHelper->currency($amountLeft, true, false);
            }
        }
        return $output;
    }
}
