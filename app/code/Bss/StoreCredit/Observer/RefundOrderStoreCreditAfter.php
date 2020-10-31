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
use Magento\Framework\App\RequestInterface;
use Bss\StoreCredit\Helper\Data;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Psr\Log\LoggerInterface;
use Bss\StoreCredit\Model\HistoryFactory;
use Magento\Framework\Event\Observer;
use Bss\StoreCredit\Model\History;

/**
 * Class RefundOrderStoreCreditAfter
 * @package Bss\StoreCredit\Observer
 */
class RefundOrderStoreCreditAfter implements ObserverInterface
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

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
     * @param RequestInterface $request
     * @param Data $bssStoreCreditHelper
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param LoggerInterface $logger
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        RequestInterface $request,
        Data $bssStoreCreditHelper,
        StoreCreditRepositoryInterface $storeCreditRepository,
        LoggerInterface $logger,
        HistoryFactory $historyFactory
    ) {
        $this->request = $request;
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->logger = $logger;
        $this->historyFactory = $historyFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $params = $this->request->getParams();
        $creditmemo = $observer->getEvent()->getCreditmemo();
        try {
            $customerId = $creditmemo->getCustomerId();
            $websiteId = $creditmemo->getStore()->getWebsiteId();
            $credit = $this->storeCreditRepository->get($customerId, $websiteId);
            $baseGrandTotal = $creditmemo->getBaseGrandTotal();
            $historyModel = $this->historyFactory->create();
            if (isset($params['creditmemo']['storecredit']) && $params['creditmemo']['storecredit']) {
                $baseStorecreditRefund = $creditmemo->getBaseBssStorecreditAmount() + $baseGrandTotal;
            } else {
                $baseStorecreditRefund = $creditmemo->getBaseBssStorecreditAmount();
            }
            if (!$credit->getId()) {
                return;
            }
            $baseAmountUpdate = $credit->getBalanceAmount();

            if ($baseStorecreditRefund) {
                $historyModel->updateHistory(
                    History::TYPE_REFUND,
                    $customerId,
                    $websiteId,
                    $baseStorecreditRefund,
                    null,
                    true,
                    $baseAmountUpdate,
                    $creditmemo->getStoreId(),
                    $creditmemo->getOrderId(),
                    $creditmemo->getId()
                );
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
