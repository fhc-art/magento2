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
namespace Bss\QuoteExtension\Controller\Quote;

use Bss\QuoteExtension\Helper\Customer\AutoLogging;
use Bss\QuoteExtension\Helper\Data;
use Bss\QuoteExtension\Model\ManageQuoteFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\QuoteFactory;

/**
 * Class View
 *
 * @package Bss\QuoteExtension\Controller\Quote
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class View extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var QuoteFactory
     */
    protected $mageQuoteFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var AutoLogging
     */
    protected $bssHelperLogging;

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param ManageQuoteFactory $quoteFactory
     * @param QuoteFactory $mageQuoteFactory
     * @param Data $helper
     * @param CheckoutSession $checkoutSession
     * @param AutoLogging $bssHelperLogging
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        ManageQuoteFactory $quoteFactory,
        QuoteFactory $mageQuoteFactory,
        Data $helper,
        CheckoutSession $checkoutSession,
        AutoLogging $bssHelperLogging
    ) {
        parent::__construct($context);
        $this->resultPageFactory    = $resultPageFactory;
        $this->coreRegistry        = $registry;
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->quoteFactory = $quoteFactory;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->bssHelperLogging = $bssHelperLogging;
    }

    /**
     * Dispatch Controller
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->bssHelperLogging->isCustomerLoggedIn()) {
            $params = $this->_request->getParams();
            $isAutoLogging = $this->bssHelperLogging->isAutoLogging();
            if (isset($params['quote_id']) && $isAutoLogging && isset($params['token'])) {
                $requestQuote = $this->quoteFactory->create()->load($params['quote_id']);
                $token = $requestQuote->getToken();
                if ($requestQuote->getEntityId() && $token == $params['token']) {
                    $quote = $this->mageQuoteFactory->create()->load($requestQuote->getQuoteId());
                    $this->bssHelperLogging->setCustomerDataLoggin($quote);
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('quoteextension/quote/view/quote_id/' . $params['quote_id']);
                } else {
                    $this->_actionFlag->set('', 'no-dispatch', true);
                }
            } else {
                $this->_actionFlag->set('', 'no-dispatch', true);
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Quote View Page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $enable = $this->helper->isEnable();
        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = $this->quoteFactory->create()->load($quoteId);
        $mageQuote = $this->mageQuoteFactory->create()->load($quote->getQuoteId());
        if ($enable && $quoteId && $quote->getEntityId() && $mageQuote->getId()) {
            $resultPage = $this->resultPageFactory->create();
            $this->coreRegistry->register('current_quote_extension', $quote);
            $this->coreRegistry->register('current_quote', $mageQuote);
            $resultPage->getConfig()->getTitle()->set(__('Quote # %1', $quote->getIncrementId()));

            /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
            $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('quoteextension/quote/history');
            }

            $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
            if ($block) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }
            $this->checkoutSession->setIsQuoteExtension($mageQuote->getId());
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('The request quote id no longer exists.'));
            return $resultRedirect->setPath('quoteextension/quote/history');
        }
    }
}
