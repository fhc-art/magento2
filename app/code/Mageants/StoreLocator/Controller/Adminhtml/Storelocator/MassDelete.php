<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Locator MassDelete Action
 */
class MassDelete  extends \Magento\Backend\App\Action
{
	/**
	 * Filter
	 *
	 * @var \Magento\Ui\Component\MassAction\Filter
	 */
    protected $filter;
	
	/**
	 * store collection factory
	 *
	 * @var Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory
	 */
    protected $collectionFactory;

	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Magento\Ui\Component\MassAction\Filter
	 * @param Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory
	 */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

	/**
	 * perform MassDelete Action
	 *
	 * @return $resultRedirect
	 */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $item->delete();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
