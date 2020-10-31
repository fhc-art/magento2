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

namespace Bss\StoreCredit\Block\Customer;

use Magento\Framework\View\Element\Template;
use Bss\StoreCredit\Model\History;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\Helper\Data;
use Bss\StoreCredit\Model\HistoryFactory;
use Bss\StoreCredit\Helper\Data as StoreCreditData;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Bss\StoreCredit\Api\HistoryRepositoryInterface;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Magento\Theme\Block\Html\Pager;

/**
 * Class Account
 * @package Bss\StoreCredit\Block\Customer
 */
class Account extends Template
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \Bss\StoreCredit\Api\HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Bss\StoreCredit\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * Construct
     *
     * @param Context $context
     * @param Data $priceHelper
     * @param HistoryFactory $historyFactory
     * @param StoreCreditData $bssStoreCreditHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param HistoryRepositoryInterface $historyRepository
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $priceHelper,
        HistoryFactory $historyFactory,
        StoreCreditData $bssStoreCreditHelper,
        OrderRepositoryInterface $orderRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        HistoryRepositoryInterface $historyRepository,
        StoreCreditRepositoryInterface $storeCreditRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->priceHelper = $priceHelper;
        $this->historyFactory = $historyFactory;
        $this->localeDate = $context->getLocaleDate();
        $this->orderRepository = $orderRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->historyRepository = $historyRepository;
        $this->storeCreditRepository = $storeCreditRepository;
    }

    /**
     * Prepare the layout of the history block.
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getHistory()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'bss.storecredit.history.pager'
            )->setAvailableLimit(
                [
                    10 => 10,
                    15 => 15,
                    20 => 20
                ]
            )->setShowPerPage(
                true
            )->setCollection(
                $this->getHistory()
            );
            $this->setChild('pager', $pager);
            $this->getHistory()->load();
        }
        return $this;
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get balance given the customer Id stored in the session.
     *
     * @return string
     */
    public function getBalanceWebsite()
    {
        $credit = $this->storeCreditRepository->get();
        $amount = 0;
        if (!empty($credit->getData())) {
            $amount = $credit->getBalanceAmount();
        }
        return $this->priceHelper->currency($amount);
    }

    /**
     * Return the History given the customer Id stored in the session.
     *
     * @return \Bss\StoreCredit\Model\History
     */
    public function getHistory()
    {
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
        $collection = $this->historyFactory->create()->loadByCustomer();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * Convert price with currency
     *
     * @param   float $price
     * @return  string
     */
    public function convertPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * Get type action by value
     *
     * @param   int $value
     * @return  string
     */
    public function getTypeAction($value)
    {
        return $this->bssStoreCreditHelper->getTypeAction($value);
    }

    /**
     * Convert update time
     *
     * @param   string $time
     * @return  string
     */
    public function formatDateTime($time)
    {
        return $this->localeDate->formatDateTime($time, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Load additional info of history by customer
     *
     * @param int|null $historyId
     * @return string
     */
    public function getAddition($historyId = null)
    {
        if ($historyId != null) {
            $history = $this->historyRepository->getById($historyId);
            if (isset($history)) {
                $type = $history->getType();
                $value = '<span>';
                switch ($type) {
                    case History::TYPE_UPDATE:
                        $value .= $history->getCommentContent();
                        break;
                    case History::TYPE_USED_IN_ORDER:
                        $order = $this->orderRepository->get($history->getOrderId());
                        $url = $this->getUrl(
                            'sales/order/view',
                            ['order_id' => $history->getOrderId()]
                        );
                        $value .= '<a href="'. $url .'"">';
                        $value .= __('Order # %1', $order->getIncrementId());
                        $value .= '</a>';
                        break;
                    case History::TYPE_REFUND:
                        $creditmemo = $this->creditmemoRepository->get($history->getCreditmemoId());
                        $url = $this->getUrl(
                            'sales/order/creditmemo',
                            ['order_id' => $history->getOrderId()]
                        );
                        $value .= '<a href="'. $url .'"">';
                        $value .= __('Credit Memo # %1', $creditmemo->getIncrementId());
                        $value .= '</a>';
                        break;
                    default:
                        break;
                }
                $value .= '</span>';
                return $value;
            }
        }
        return null;
    }
}
