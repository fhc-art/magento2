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

use Magento\Framework\Mail\Template\TransportBuilder;
use Bss\StoreCredit\Helper\Data as StoreCreditData;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Area;

/**
 * Class Email
 * @package Bss\StoreCredit\Model
 */
class Email
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $storeCreditData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreCreditData $storeCreditData
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param Data $priceHelper
     * @param StateInterface $inlineTranslation
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreCreditData $storeCreditData,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        Data $priceHelper,
        StateInterface $inlineTranslation,
        CustomerFactory $customerFactory
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeCreditData = $storeCreditData;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->priceHelper = $priceHelper;
        $this->inlineTranslation = $inlineTranslation;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param $storeId
     * @param $customerId
     * @param null $credit
     * @param null $comment
     */
    public function sendMailNotify($storeId, $customerId, $credit = null, $comment = null)
    {
        try {
            $this->inlineTranslation->suspend();
            $store = $this->storeManager->getStore($storeId);
            $balanceChange = '';
            $balanceAmount = '';
            if ($credit) {
                $balanceChange = $this->priceHelper->currencyByStore($credit->getChangeAmount(), $store);
                $balanceAmount = $this->priceHelper->currencyByStore($credit->getBalanceAmount(), $store);
            }
            $customer = $this->customerFactory->create()->load($customerId);
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->storeCreditData->getEmailConfig('template', $storeId))
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )
                ->setTemplateVars(
                    [
                        'store' => $store,
                        'customer' => $customer->getName(),
                        'comment' => $comment,
                        'balance_change' => $balanceChange,
                        'balance_amount' => $balanceAmount
                    ]
                )
                ->setFrom($this->storeCreditData->getEmailConfig('identity', $storeId))
                ->addTo($customer->getEmail(), $customer->getName())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
