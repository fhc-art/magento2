<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

/**
 * NewAction for Store
 */
class NewAction extends \Magento\Backend\App\Action
{
	/**
	 * store result forward factory
	 *
	 * @var \Magento\Backend\Model\View\Result\ForwardFactory
	 */
    protected $resultForwardFactory;

	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Magento\Backend\Model\View\Result\ForwardFactory
	 */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) 
    {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
	
	/**
	 * {@inheritdoc}
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_StoreLocator::save');
    }
	
	/**
	 * perform execute method for newAction
	 *
	 * @return $resultRedirect
	 */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
