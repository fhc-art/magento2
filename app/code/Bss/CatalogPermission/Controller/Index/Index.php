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
 * @package    Bss_CatalogPermission
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CatalogPermission\Controller\Index;

use Bss\CatalogPermission\Helper\ModuleConfig;
use Magento\Cms\Helper\Page;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Index
 *
 * @package Bss\CatalogPermission\Controller\Index
 */
class Index extends Action
{
    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $helperCmsPage;

    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Cms\Helper\Page $helperCmsPage
     * @param \Bss\CatalogPermission\Helper\ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        Page $helperCmsPage,
        ModuleConfig $moduleConfig
    ) {
        $this->helperCmsPage = $helperCmsPage;
        $this->moduleConfig = $moduleConfig;
        parent::__construct($context);
    }

    /**
     * Controller Execute
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $pageType = $this->getRequest()->getParam('pagetype');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($pageType == 'category' || $pageType == 'product') {
            $pageId = $this->moduleConfig->getPageIdToRedirect();
            $urlPage = $this->helperCmsPage->getPageUrl($pageId);
            $message = $this->moduleConfig->getErrorMessage();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }

            if (!empty($urlPage)) {
                $resultRedirect->setUrl($urlPage);
            } else {
                $resultRedirect->setPath('');
            }
            return $resultRedirect;
        } elseif ($pageType == 'cmsPage') {
            $pageId = $this->moduleConfig->getPageIdToRedirectCms();
            $urlPage = $this->helperCmsPage->getPageUrl($pageId);
            $message = $this->moduleConfig->getErrorMessageCms();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }
            if (!empty($urlPage)) {
                $resultRedirect->setUrl($urlPage);
                return $resultRedirect;
            } else {
                $resultRedirect->setPath('');
                return $resultRedirect;
            }
        }
        return $resultRedirect->setPath('');
    }
}
