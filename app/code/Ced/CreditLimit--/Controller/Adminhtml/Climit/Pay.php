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
 * Class Pay
 * @package Ced\CreditLimit\Controller\Adminhtml\Climit
 */
class Pay extends \Magento\Backend\App\Action
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
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date   $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
    }

    
    
    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Ced_CreditLimit::creditlimit')->_addBreadcrumb(__('Manage  Credit Balance'));
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
        $this->_coreRegistry->register('credit_limit',$model);
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Limit no longer exists.'));
                $this->_redirect('*/*/index');
                return;
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Manage Balance'), __('Manage Balance'));
        $resultPage->addBreadcrumb(__('Manage Balance'), __('Manage Balance'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Balance'));
        return $resultPage;
    }
}
