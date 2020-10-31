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

namespace Bss\StoreCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Bss\StoreCredit\Helper\Data;
use Bss\StoreCredit\Model\HistoryFactory;
use Magento\Framework\Event\Observer;
use Bss\StoreCredit\Model\History;

/**
 * Class SalesEventQuoteSubmitSuccessObserver
 * @package Bss\StoreCredit\Observer
 */
class SalesEventQuoteSubmitSuccessObserver implements ObserverInterface
{
    /**
     * @var \Bss\StoreCredit\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param Data $bssStoreCreditHelper
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreCreditRepositoryInterface $storeCreditRepository,
        Data $bssStoreCreditHelper,
        HistoryFactory $historyFactory
    ) {
        $this->storeManager = $storeManager;
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->historyFactory = $historyFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->bssStoreCreditHelper->getGeneralConfig('active')) {
            return;
        }
        $order = $observer->getEvent()->getOrder();
        $amount = $observer->getEvent()->getQuote()->getBssStorecreditAmount();
        $baseAmount = $observer->getEvent()->getQuote()->getBaseBssStorecreditAmount();
        $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
        $customerId = $order->getCustomerId();
        if ($baseAmount && $amount && $customerId) {
            $credit = $this->storeCreditRepository->get($customerId, $websiteId);
            $historyModel = $this->historyFactory->create();
            $order->setBssStorecreditAmount($amount)
                ->setBaseBssStorecreditAmount($baseAmount)
                ->save();
            $amountAfter = $credit->getBalanceAmount() - $baseAmount;
            $credit->setBalanceAmount($amountAfter)->save();
            $historyModel->updateHistory(
                History::TYPE_USED_IN_ORDER,
                $customerId,
                $websiteId,
                -$baseAmount,
                null,
                true,
                $amountAfter,
                $order->getStoreId(),
                $order->getId()
            );
        }
    }
}
