<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CreditLimit
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CreditLimit\Controller\Adminhtml\Climit;
/**
 * Class Edit
 * @package Ced\CreditLimit\Controller\Adminhtml\Climit
 */
class Edit extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context   $context
     * @param \Magento\Framework\Registry  $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }
 
    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Ced_CreditLimit::creditlimit')->_addBreadcrumb(__('Manage Credit Limits'), __('Credit Limits'));
        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_CreditLimit::creditlimit');
    }
    
    /**
     * Promo quote edit action
     *
     * @return  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ced\CreditLimit\Model\CreditLimit');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Limit no longer exists.'));
                $this->_redirect('*/*/index');
                return;
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        
        $this->_coreRegistry->register('credit_limit', $model);
        $this->_initAction();

        $this->_addBreadcrumb($id ? __('Edit Customer Limit') : __('New Limit'), $id ? __('Edit Customer Limit') : __('New Limit'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Customer Limit') : __('New Limit')
        );

        $this->_view->renderLayout();
    }
}
