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
 * @category   BSS
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\QuoteExtension\Block\QuoteExtension;

use Bss\QuoteExtension\Model\Config\Source\Status;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Class History
 *
 * @package Bss\QuoteExtension\Block\QuoteExtension
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'quoteextension/history.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $quotes;

    /**
     * @var CollectionFactoryInterface
     */
    private $quoteCollectionFactory;

    /**
     * @var \Bss\QuoteExtension\Helper\QuoteExtension\Status
     */
    protected $helperStatus;

    /**
     * @var \Bss\QuoteExtension\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Bss\QuoteExtension\Helper\CartHidePrice
     */
    protected $cartHidePrice;

    /**
     * History constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\QuoteExtension\Model\ResourceModel\ManageQuote\CollectionFactory $quoteCollectionFactory
     * @param \Bss\QuoteExtension\Helper\QuoteExtension\Status $helperStatus
     * @param \Bss\QuoteExtension\Helper\Data $helperData
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Bss\QuoteExtension\Helper\CartHidePrice $cartHidePrice
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\QuoteExtension\Model\ResourceModel\ManageQuote\CollectionFactory $quoteCollectionFactory,
        \Bss\QuoteExtension\Helper\QuoteExtension\Status $helperStatus,
        \Bss\QuoteExtension\Helper\Data $helperData,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Bss\QuoteExtension\Helper\CartHidePrice $cartHidePrice,
        array $data = []
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->helperStatus = $helperStatus;
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->cartHidePrice = $cartHidePrice;
        parent::__construct($context, $data);
    }

    /**
     * { @inheritdoc }
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotes'));
    }

    /**
     * Get Request Quotes by customer
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getRequestQuotes()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->quotes) {
            $this->quotes = $this->quoteCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter('main_table.customer_id', $customerId)->setOrder('entity_id');
        }
        return $this->quotes;
    }

    /**
     * @param $id
     * @return bool|\Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote($id)
    {
        try {
            return $this->quoteRepository->get($id);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * { @inheritdoc }
     *
     * @return $this|\Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuotes()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'quoteextension.quote.history.pager'
            )->setCollection(
                $this->getQuotes()
            );
            $this->setChild('pager', $pager);
            $this->getQuotes()->load();
        }
        return $this;
    }

    /**
     * { @inheritdoc }
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get Request Quote View Url
     *
     * @param object $quote
     * @return string
     */
    public function getViewUrl($quote)
    {
        return $this->getUrl('quoteextension/quote/view', ['quote_id' => $quote->getId()]);
    }

    /**
     * Can show current total
     *
     * @param \Bss\QuoteExtension\Model\ManageQuote $manaQuote
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function canShowCurrentSubtotal($manaQuote)
    {
        if ($manaQuote->getStatus() === Status::STATE_PENDING
            || $manaQuote->getStatus() === Status::STATE_CANCELED
            || $manaQuote->getStatus() === Status::STATE_REJECTED
        ) {
            foreach ($this->getQuote($manaQuote->getQuoteId())->getAllVisibleItems() as $item) {
                /* @var $item \Magento\Quote\Model\Quote\Item */
                if ($item->getProductType() == 'configurable') {
                    $parentProductId = $item->getProductId();
                    $childProductSku = $item->getSku();
                    $canShowPrice = $this->cartHidePrice->canShowPrice($parentProductId, $childProductSku);
                } else {
                    $canShowPrice = $this->cartHidePrice->canShowPrice($item->getProductId(), false);
                }
                if (!$canShowPrice) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return \Bss\QuoteExtension\Helper\QuoteExtension\Status
     */
    public function getHelperStatus()
    {
        return $this->helperStatus;
    }

    /**
     * @return \Bss\QuoteExtension\Helper\Data
     */
    public function getHelperData()
    {
        return $this->helperData;
    }
}
