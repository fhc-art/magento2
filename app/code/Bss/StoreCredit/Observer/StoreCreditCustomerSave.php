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
use Bss\StoreCredit\Model\CreditFactory;
use Bss\StoreCredit\Helper\Data;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Psr\Log\LoggerInterface;
use Bss\StoreCredit\Model\HistoryFactory;
use Magento\Framework\Event\Observer;
use Bss\StoreCredit\Model\History;

/**
 * Class StoreCreditCustomerSave
 * @package Bss\StoreCredit\Observer
 */
class StoreCreditCustomerSave implements ObserverInterface
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var Bss\StoreCredit\Model\CreditFactory
     */
    private $creditFactory;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Bss\StoreCredit\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * @param CreditFactory $creditFactory
     * @param Data $bssStoreCreditHelper
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param LoggerInterface $logger
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        CreditFactory $creditFactory,
        Data $bssStoreCreditHelper,
        StoreCreditRepositoryInterface $storeCreditRepository,
        LoggerInterface $logger,
        HistoryFactory $historyFactory
    ) {
        $this->creditFactory = $creditFactory;
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->logger = $logger;
        $this->historyFactory = $historyFactory;
    }

    /**
     * Credit update for customer after save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
        $params = $observer->getRequest()->getParams();
        $customerId = $customer->getId();

        if (isset($params['bss_storecredit_balance']) && $customerId) {
            $websiteId = (int) $params['bss_storecredit_balance']['website_id'];
            $amount = (float) $params['bss_storecredit_balance']['amount_value'];
            $comment = $params['bss_storecredit_balance']['comment_content'];
            $isNotified = (boolean) $params['bss_storecredit_balance']['is_notify'];
            $storeId = $params['bss_storecredit_balance']['store_id'];
            try {
                $credit = $this->storeCreditRepository->get($customerId, $websiteId);
                $historyModel = $this->historyFactory->create();
                if (isset($credit) && $credit->getBalanceId()) {
                    $amountAfter = $credit->getBalanceAmount() + $amount;
                    $credit->setBalanceAmount($amountAfter)
                        ->save();
                } else {
                    $amountAfter = $amount;
                    $this->creditFactory->create()
                        ->setBalanceAmount($amountAfter)
                        ->setWebsiteId($websiteId)
                        ->setCustomerId($customerId)
                        ->save();
                }
                $historyModel->updateHistory(
                    History::TYPE_UPDATE,
                    $customerId,
                    $websiteId,
                    $amount,
                    $comment,
                    $isNotified,
                    $amountAfter,
                    $storeId
                );
            } catch (\Exception $e) {
                $this->logger->log($e->getMessage());
            }
        }
    }
}
