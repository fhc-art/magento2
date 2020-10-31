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

namespace Bss\StoreCredit\Block\Adminhtml\Order\Create;

use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Store\Model\StoreFactory;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data;

/**
 * Class Credit
 * @package Bss\StoreCredit\Block\Adminhtml\Order\Create
 */
class Credit extends AbstractCreate
{
    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    private $storeFactory;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreFactory $storeFactory
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param Data $priceHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        StoreFactory $storeFactory,
        StoreCreditRepositoryInterface $storeCreditRepository,
        Data $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
        $this->storeFactory = $storeFactory;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_store_credit');
    }

    /**
     * Get store credit cutomer total
     *
     * @return float|bool
     */
    public function getStoreCreditTotal()
    {
        $quote = $this->getQuote();
        $store = $this->storeFactory->create()->load($quote->getStoreId());
        $credit = $this->storeCreditRepository->get($quote->getCustomerId(), $store->getWebsiteId());
        if ($credit->getId()) {
            $amountUsed = $this->getQuote()->getBaseBssStorecreditAmount();
            $amountLeft = $credit->getBalanceAmount() - $amountUsed;
            return $this->priceHelper->currencyByStore($amountLeft, $quote->getStoreId());
        }
        return false;
    }

    /**
     * Get store credit cutomer used
     *
     * @return float|bool
     */
    public function getStoreCreditUsed()
    {
        $quote = $this->getQuote();
        $amount = $quote->getBaseBssStorecreditAmount();
        if ($quote->getId() && $amount > 0) {
            return $this->priceHelper->currencyByStore($amount, $quote->getStoreId());
        }
        return false;
    }
}
