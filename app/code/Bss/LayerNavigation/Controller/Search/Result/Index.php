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
 * @category  BSS
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Controller\Search\Result;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\Context;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $helper;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $bssHelper;

    /**
     * @var Redirect
     */
    protected $resultRedirectFactory;
    
    /**
     * Index constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\CatalogSearch\Helper\Data $helper
     * @param \Bss\LayerNavigation\Helper\Data $bssHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\CatalogSearch\Helper\Data $helper,
        \Bss\LayerNavigation\Helper\Data $bssHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->bssHelper = $bssHelper;
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);

        $query = $this->queryFactory->get();

        $query->setStoreId($this->storeManager->getStore()->getId());

        $resultPage = $this->resultPageFactory->create();
        $resultJson = $this->resultJsonFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($query->getQueryText() != '') {
            if ($this->helper->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                try {
                    $query->saveIncrementalPopularity();

                    if ($query->getRedirect()) {
                        $resultRedirect->setUrl($query->getRedirect());
                        return $resultRedirect;
                    }
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
            $request = $this->getRequest();
            if ($request->getMethod() == 'POST'
                && $request->isAjax()
                && $this->bssHelper->isEnabled()
                && $this->bssHelper->getUserAjax()) {
                $this->helper->checkNotes();
                $navigation = $resultPage->getLayout()->getBlock('catalogsearch.leftnav.bss');
                $products = $resultPage->getLayout()->getBlock('search.result');
                $result = [
                    'products' => $products->toHtml(),
                    'navigation' => $navigation->toHtml()
                ];

                return $resultJson->setData($result);
            }
        } else {
            $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
            return $resultRedirect;
        }
        return $resultPage;
    }
}
