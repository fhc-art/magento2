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
 * Class Save
 *
 * @package Bss\QuoteExtension\Controller\Adminhtml\Manage
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Save extends AbstractController
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('quote_manage_id');
        if ($id) {
            try {
                $data = $this->getRequest()->getPostValue();
                $this->manageQuote->load($id);

                if ($this->manageQuote->getId()) {
                    $mageQuote = $this->getQuote();
                    $mageQuote->setLogComment($data['customer_note']);
                    $mageQuote->setAreaLog('admin');
                    $version = $this->manageQuote->getVersion();
                    if ($this->backendSession->getHasChange() || $data['customer_note']) {
                        $this->quoteVersion->setDataToQuoteVersion($mageQuote, $this->manageQuote);
                        $version++;
                        $this->backendSession->setHasChange(false);
                    }
                    $this->manageQuote->setNotSendEmail(true);
                    $oldQuote = $this->quoteRepository->get($this->manageQuote->getTargetQuote());
                    $backendQuote = $this->manageQuote->getBackendQuoteId();
                    $this->manageQuote->setBackendQuoteId(null);
                    $this->manageQuote->setTargetQuote($backendQuote);
                    $this->manageQuote->setData('expiry', $data['expiry']);
                    $this->manageQuote->setData('status', $data['status']);
                    $this->manageQuote->setData('version', $version);
                    $this->manageQuote->save();
                    $this->quoteRepository->delete($oldQuote);
                    $this->messageManager->addSuccessMessage(__('You saved the quote'));
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->manageQuote->getId()]);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->manageQuote->getId()]);
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a quote.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_QuoteExtension::save_quote');
    }
}
