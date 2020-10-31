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

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class OrderCreateApplyStoreCredit
 * @package Bss\StoreCredit\Observer
 */
class OrderCreateApplyStoreCredit implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $data = $observer->getRequestModel()->getPost('order');
        $amount = '';
        if (isset($data) && isset($data['storecredit']['amount'])) {
            $amount = $data['storecredit']['amount'];
        }
        $quote = $observer->getOrderCreateModel()->getQuote();
        $totals_amount = $quote->getTotals()['grand_total']->getValue();
        if ($amount != '') {
            $amount = (float) $amount;
            if ($amount >= 0) {
                if ($amount > $totals_amount) {
                    $this->messageManager->addErrorMessage(__('Make sure you don\'t apply store credit more than order total.'));
                } else {
                    $quote->setBaseBssStorecreditAmountInput($amount);
                    $this->messageManager->addSuccessMessage(__('Success.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('Error'));
            }
        }
    }
}
