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
 * Class Edit
 *
 * @package Bss\QuoteExtension\Controller\Adminhtml\Manage
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Bss\QuoteExtension\Model\ManageQuote
     */
    protected $manageQuote;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObject;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $newQuote;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Bss\QuoteExtension\Model\ManageQuote $manageQuote
     * @param \Magento\Framework\DataObjectFactory $dataObject
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Backend\Model\Session $backendSession,
        \Bss\QuoteExtension\Model\ManageQuote $manageQuote,
        \Magento\Framework\DataObjectFactory $dataObject,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->quoteRepository = $quoteRepository;
        $this->backendSession = $backendSession;
        $this->manageQuote = $manageQuote;
        $this->dataObject = $dataObject;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Execute Edit
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');

        if ($id) {
            $this->manageQuote->load($id);
            if ($this->manageQuote->getId()) {
                $this->initRuleData();
                $data = $this->backendSession->getFormData(true);
                if (!empty($data)) {
                    $this->manageQuote->setData($data);
                }

                if (!$this->manageQuote->getTargetQuote()) {
                    $this->setTargetQuote();
                }

                if ($this->manageQuote->getBackendQuoteId()) {
                    $this->clearBackendQuoteId();
                }
                $this->setBackendQuoteId();
                $this->newQuote = $this->quoteRepository->get($this->manageQuote->getBackendQuoteId());
                $this->backendSession->setQuoteExtensionId($this->newQuote->getId());
                $this->backendSession->setQuoteManageId($id);
                $this->coreRegistry->register('mage_quote', $this->newQuote);
                $this->coreRegistry->register('quoteextension_quote', $this->manageQuote);

                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->resultPageFactory->create();

                $resultPage->setActiveMenu('Magento_Sales::sales')
                    ->addBreadcrumb(__('Request4Quote'), __('Request4Quote'))
                    ->addBreadcrumb(__('Quote'), __('Quote'));
                $resultPage->addBreadcrumb(__('Edit Quote'), __('Edit Quote'));
                $resultPage->getConfig()->getTitle()->prepend(__('Request4Quote'));
                $resultPage->getConfig()->getTitle()->prepend('#' . $this->manageQuote->getIncrementId());
                return $resultPage;
            }
        }
        $this->messageManager->addErrorMessage(__('This Quote no longer exists'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Set Rule Data in page
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initRuleData()
    {
        $mageQuote = $this->quoteRepository->get($this->manageQuote->getQuoteId());
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
     * { @inheritdoc }
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_QuoteExtension::edit_quote');
    }

    /**
     * Set Target Quote
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function setTargetQuote()
    {
        $mageQuote = $this->quoteRepository->get($this->manageQuote->getQuoteId());
        $this->newQuote = $this->quoteFactory->create();
        $mageQuote->cloneQuoteExtension($this->newQuote);
        $this->quoteRepository->save($this->newQuote);
        $this->_eventManager->dispatch(
            'quote_extension_clone_after',
            ['quote' => $this->newQuote]
        );
        $quoteId = $this->newQuote->getId();
        $this->newQuote = $this->quoteRepository->get($quoteId);
        $this->manageQuote->setNotSendEmail(true);
        $this->manageQuote->setTargetQuote($this->newQuote->getId())->save();
        return $this;
    }

    /**
     * Get Extension Draft
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function setBackendQuoteId()
    {
        $mageQuote = $this->quoteRepository->get($this->manageQuote->getTargetQuote());
        $this->newQuote = $this->quoteFactory->create();
        $mageQuote->cloneQuoteExtension($this->newQuote);
        $this->quoteRepository->save($this->newQuote);
        $this->_eventManager->dispatch(
            'quote_extension_clone_after',
            ['quote' => $this->newQuote]
        );
        $quoteId = $this->newQuote->getId();
        $this->newQuote = $this->quoteRepository->get($quoteId);
        $this->manageQuote->setNotSendEmail(true);
        $this->manageQuote->setBackendQuoteId($this->newQuote->getId())->save();
        return $this;
    }

    /**
     * delete backend quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function clearBackendQuoteId()
    {
        $backendQuote = $this->quoteRepository->get($this->manageQuote->getBackendQuoteId());
        $this->quoteRepository->delete($backendQuote);
    }
}
