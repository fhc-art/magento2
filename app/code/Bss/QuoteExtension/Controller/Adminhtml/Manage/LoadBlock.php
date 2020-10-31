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
namespace Bss\QuoteExtension\Controller\Adminhtml\Manage;

/**
 * Class LoadBlock
 *
 * @package Bss\QuoteExtension\Controller\Adminhtml\Manage
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class LoadBlock extends \Bss\QuoteExtension\Controller\Adminhtml\Manage\Edit
{
    /**
     * @var
     */
    protected $quoteExtension;

    /**
     * @var
     */
    protected $manaQuoteExtension;

    /**
     * @var \Bss\QuoteExtension\Helper\Admin\Edit\LoadBlock
     */
    protected $loadBlock;

    /**
     * LoadBlock constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Bss\QuoteExtension\Model\ManageQuote $manageQuote
     * @param \Magento\Framework\DataObjectFactory $dataObject
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Bss\QuoteExtension\Helper\Admin\Edit\LoadBlock $loadBlock
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Backend\Model\Session $backendSession,
        \Bss\QuoteExtension\Model\ManageQuote $manageQuote,
        \Magento\Framework\DataObjectFactory $dataObject,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Bss\QuoteExtension\Helper\Admin\Edit\LoadBlock $loadBlock
    ) {
        $this->loadBlock = $loadBlock;
        parent::__construct(
            $context,
            $coreRegistry,
            $resultPageFactory,
            $quoteRepository,
            $backendSession,
            $manageQuote,
            $dataObject,
            $quoteFactory
        );
    }

    /**
     * Loading page block
     *
     * @return \Magento\Backend\Model\View\Result\Page
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $request = $this->getRequest();

        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        try {
            $this->initRuleData();
            $this->processActionData();
        } catch (\Exception $e) {
            $this->reloadQuote();
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }
        $this->coreRegistry->register('mage_quote', $this->getQuote());
        $this->coreRegistry->register('quoteextension_quote', $this->getManaQuote());
        $resultPage = $this->resultPageFactory->create();
        if ($asJson) {
            $resultPage->addHandle('sales_order_create_load_block_json');
        } else {
            $resultPage->addHandle('sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $resultPage->addHandle('bss_quote_extension_load_block_' . $block);
            }
        }
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->loadBlock->setContent($result);
    }

    protected function initRuleData()
    {
        $mageQuote = $this->getQuote();
        $object = $this->dataObject->create();
        $object->setData(
            [
                'store_id' => $mageQuote->getStore()->getId(),
                'website_id' => $mageQuote->getStore()->getWebsiteId(),
                'customer_group_id' => $mageQuote->getCustomerGroupId()
            ]
        );
        $this->coreRegistry->register(
            'rule_data',
            $object
        );

        return $this;
    }

    /**
     * Save Quote
     *
     * return $this
     */
    protected function processActionData()
    {
        $this->loadBlock->createQuote()->setQuote($this->getQuote());
        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->processFiles($items);
            $this->loadBlock->createQuote()->updateQuoteItems($items);
            $this->backendSession->setHasChange(true);
        }

        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items')
        ) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->processFiles($items);
            $this->loadBlock->createQuote()->addProducts($items);
            $this->backendSession->setHasChange(true);
        }
        if ($this->getRequest()->has('quote')
        ) {
            $quote = $this->getRequest()->getPost('quote');
            if (isset($quote['quote_shipping_price'])) {
                $this->loadBlock->createQuote()->getQuote()->setData('quote_shipping_price', $quote['quote_shipping_price']);
            }
            if (isset($quote['shipping_method'])) {
                $this->loadBlock->createQuote()->setShippingMethod($quote['shipping_method']);
            }
            $this->backendSession->setHasChange(true);
        }
        /**
         * Remove quote item
         */
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->loadBlock->createQuote()->removeItem($removeItemId, $removeFrom);
            $this->backendSession->setHasChange(true);
        }
        $this->loadBlock->createQuote()->collectShippingRates();
        $this->loadBlock->createQuote()->setRecollect(true);
        $this->loadBlock->createQuote()->saveQuote();
        return $this;
    }

    /**
     * Process buyRequest file options of items
     *
     * @param array $items
     * @return array
     */
    protected function processFiles($items)
    {
        foreach ($items as $id => $item) {
            $buyRequest = $this->loadBlock->initDataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $this->loadBlock->getInfoBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }

    /**
     * Load Quote By Quote Id
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function reloadQuote()
    {
        $quoteId = $this->backendSession->getQuoteExtensionId();
        $this->getQuote()->load($quoteId);
        return $this;
    }

    /**
     * Retrieve quote object
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuote()
    {
        if (!$this->quoteExtension) {
            $quoteId = $this->getRequest()->getPost('quote_id')
                ? $this->getRequest()->getPost('quote_id') : $this->backendSession->getQuoteExtensionId();
            $this->quoteExtension = $this->quoteRepository->get($quoteId);
        }
        return $this->quoteExtension;
    }

    /**
     * Set Quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     */
    protected function setQuote($quote)
    {
        $this->quoteExtension = $quote;
        return $this;
    }

    /**
     * Get Request Quote
     *
     * @return \Bss\QuoteExtension\Model\ManageQuote
     */
    protected function getManaQuote()
    {
        if (!$this->manaQuoteExtension) {
            $quoteManageId = $this->backendSession->getQuoteManageId();
            $this->manaQuoteExtension = $this->manageQuote->load($quoteManageId);
        }
        return $this->manaQuoteExtension;
    }
}
