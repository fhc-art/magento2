<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Result extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \DCKAP\Ordersearch\Model\Ordersearch
     */
    protected $orderSearchModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \DCKAP\Ordersearch\Model\ResourceModel\Ordersearch $orderSearchModel
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \DCKAP\Ordersearch\Model\ResourceModel\Ordersearch $orderSearchModel
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderSearchModel = $orderSearchModel;
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context);
    }

    /**
     * Customer order Search
     *
     * @return \DCKAP\Ordersearch\View\Result\Page
     */
    public function execute()
    {

        $dataObject = $this->getRequest()->getPost();

        if ($this->getRequest()->isAjax()) {
            if ($dataObject) {
                $result = $this->orderSearchModel->getOrders($dataObject);
                $this->coreRegistry->register('searchresult', $result);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Order Search'));
        $resultPage->addHandle('ordersearch_index_result');
        return $resultPage;
    }
}
