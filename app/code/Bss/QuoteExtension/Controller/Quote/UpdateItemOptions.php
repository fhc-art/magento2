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

use Bss\QuoteExtension\Model\QuoteExtension as CustomerQuoteExtension;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateItemOptions
 * @package Bss\QuoteExtension\Controller\Quote
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class UpdateItemOptions extends \Bss\QuoteExtension\Controller\Quote
{
    /**
     * @var \Bss\QuoteExtension\Helper\QuoteExtension\AddToQuote
     */
    protected $helperAddToQuote;

    /**
     * Add constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $quoteExtensionSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerQuoteExtension $quoteExtension
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Bss\QuoteExtension\Model\ManageQuote $manageQuote
     * @param \Bss\QuoteExtension\Helper\QuoteExtension\AddToQuote $helperAddToQuote
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $quoteExtensionSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerQuoteExtension $quoteExtension,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Bss\QuoteExtension\Model\ManageQuote $manageQuote,
        \Bss\QuoteExtension\Helper\QuoteExtension\AddToQuote $helperAddToQuote
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $quoteExtensionSession,
            $storeManager,
            $formKeyValidator,
            $quoteExtension,
            $manageQuote,
            $resultPageFactory
        );
        $this->helperAddToQuote = $helperAddToQuote;
    }

    /**
     * Update product configuration for a quote extension item
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = [];
        }
        try {
            $this->fillterQty($params);
            $quoteItem = $this->quoteExtensionSession->getQuoteExtension()->getItemById($id);
            $this->validateItem($quoteItem, true);
            $item = $this->quoteExtension->updateItem($id, $this->helperAddToQuote->createObject($params));
            $this->validateItem($item);
            $this->quoteExtension->save();

            $this->_eventManager->dispatch(
                'checkout_cart_update_item_complete',
                ['item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
            if (!$this->quoteExtensionSession->getNoCartRedirect(true)) {
                if (!$this->quoteExtensionSession->getQuoteExtension()->getHasError()) {
                    $message = __(
                        '%1 was updated in your quote extension.',
                        $item->getProduct()->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
                return $this->_goBack($this->_url->getUrl('quoteextension/quote'));
            }
        } catch (LocalizedException $e) {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                $this->messageManager->addErrorMessage($message);
            }
            return $this->_goBack();
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t update the item right now.'));
            return $this->_goBack();
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }

    /**
     * @param $params
     */
    protected function fillterQty(&$params)
    {
        if (isset($params['qty'])) {
            $filter = $this->helperAddToQuote->getLocalized();
            $params['qty'] = $filter->filter($params['qty']);
        }
    }

    /**
     * @param $item
     * @param bool $isQuoteItem
     * @throws LocalizedException
     */
    protected function validateItem($item, $isQuoteItem = false)
    {
        if ($isQuoteItem && !$item) {
            throw new LocalizedException(
                __("The quote item isn't found. Verify the item and try again.")
            );
        }
        if (is_string($item)) {
            throw new LocalizedException(__($item));
        }
        if ($item->getHasError()) {
            throw new LocalizedException(__($item->getMessage()));
        }
    }
}
