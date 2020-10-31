<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

/**
 * Delete store 
 */
class Delete extends \Magento\Backend\App\Action
{
	/**
	 * Check permission for passed action
	 *
	 * @return $this
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_StoreLocator::StoreLocator_delete');
    }

	/**
	 * delete action execute 
	 *
	 * @return $resultRedirect
	 */
    public function execute()
    {
        $id = $this->getRequest()->getParam('store_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create('Mageants\StoreLocator\Model\ManageStore');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The Record has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['store_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a record to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
