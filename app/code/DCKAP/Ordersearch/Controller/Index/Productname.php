<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Result\PageFactory;

class Productname extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \DCKAP\Ordersearch\Model\Products
     */
    protected $orderProducts;

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
     * @param \DCKAP\Ordersearch\Model\ResourceModel\Products $orderProducts
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \DCKAP\Ordersearch\Model\ResourceModel\Products $orderProducts
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderProducts = $orderProducts;
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

        $dataObject = $this->getRequest()->getParams();

        if ($this->getRequest()->isAjax()) {
            if ($dataObject) {
                $result = $this->orderProducts->getProducts($dataObject);
                $this->coreRegistry->register('suggestproduct', $result);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Productname'));
        $resultPage->addHandle('ordersearch_index_productname');
        return $resultPage;
    }
}
