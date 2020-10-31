<?php

namespace Bss\CustomizeCreditLimit\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Result extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \DCKAP\Ordersearch\Model\ResourceModel\Ordersearch $orderSearchModel
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditSearch $creditSearch
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->creditSearchModel = $creditSearch;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        $dataObject = $this->getRequest()->getPost();
        if ($this->getRequest()->isAjax()) {
            if ($dataObject) {
                $result = $this->creditSearchModel->getOrders($dataObject);
                $this->coreRegistry->register('creditsearchresult', $result);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Invoice Payment Search'));
        $resultPage->addHandle('creditsearch_index_result');
        return $resultPage;
    }
}
