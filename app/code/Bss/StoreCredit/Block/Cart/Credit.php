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

namespace Bss\StoreCredit\Block\Cart;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\SessionFactory as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\Helper\Data;
use Bss\StoreCredit\Helper\Data as StoreCreditData;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Credit
 * @package Bss\StoreCredit\Block\Cart
 */
class Credit extends Template
{
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param Context $context
     * @param Data $priceHelper
     * @param CheckoutSession $checkoutSession
     * @param StoreCreditData $bssStoreCreditHelper
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $priceHelper,
        CheckoutSession $checkoutSession,
        StoreCreditData $bssStoreCreditHelper,
        StoreCreditRepositoryInterface $storeCreditRepository,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->priceHelper = $priceHelper;
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->checkoutSession = $checkoutSession;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get store credit customer login
     *
     * @return string|bool
     */
    public function getStoreCreditTotal()
    {
        $amountUsed = $this->checkoutSession->create()->getQuote()->getBaseBssStorecreditAmount();
        $credit = $this->storeCreditRepository->get();
        if ($credit->getId()) {
            $amountLeft = $credit->getBalanceAmount() - $amountUsed;
            return $this->priceHelper->currency($amountLeft, true, false);
        }
        return false;
    }

    /**
     * Get store credit customer used
     *
     * @return float
     */
    public function getStoreCreditUsed()
    {
        $quote = $this->checkoutSession->create()->getQuote();
        $balanceUsed = $quote->getBssStorecreditAmount();
        if ($quote->getId() && $balanceUsed) {
            return $this->priceCurrency->round($balanceUsed);
        }
        return 0;
    }

    /**
     * @return bool
     */
    public function isDisplay()
    {
        return $this->bssStoreCreditHelper->getGeneralConfig('cart_page_display');
    }
}
