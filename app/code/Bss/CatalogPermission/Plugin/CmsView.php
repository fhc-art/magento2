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

namespace Bss\CatalogPermission\Plugin;

/**
 * Class CmsView
 * @package Bss\CatalogPermission\Plugin
 */
class CmsView
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Bss\CatalogPermission\Helper\Data
     */
    protected $helper;

    /**
     * CmsView constructor.
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Bss\CatalogPermission\Helper\ModuleConfig $moduleConfig
     * @param \Bss\CatalogPermission\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Bss\CatalogPermission\Helper\ModuleConfig $moduleConfig,
        \Bss\CatalogPermission\Helper\Data $helper
    ) {
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->customerSession = $customerSession;
        $this->pageFactory = $pageFactory;
        $this->moduleConfig = $moduleConfig;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Cms\Controller\Page\View $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     */
    public function aroundExecute(
        \Magento\Cms\Controller\Page\View $subject,
        \Closure $proceed
    ) {
        $result = $proceed();
        $pageId = $subject->getRequest()->getParam('page_id', $subject->getRequest()->getParam('id', false));
        $page = $this->getCmsPageById($pageId);
        $data = $page->getData();
        $enableCmsPagePermission = $this->moduleConfig->enableCmsPagePermission();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $arrCustomerGroup = json_decode($data['bss_customer_group']);
        $redirectPageId = $this->moduleConfig->getPageIdToRedirectCms();
        if (is_array($arrCustomerGroup) &&
            in_array($customerGroupId, $arrCustomerGroup) &&
            $redirectPageId != $pageId &&
            $enableCmsPagePermission
        ) {
            $pageRedirect = $this->getCmsPageById($redirectPageId);
            $redirectPath = $this->getRedirectPage($pageRedirect);
            $message = $this->moduleConfig->getErrorMessage();
            if ($redirectPath !== false) {
                $this->messageManager->addErrorMessage($message);
                return $this->redirectFactory->create()->setPath($redirectPath);
            }
        }
        return $result;
    }

    /**
     * @param $pageRedirect
     * @return bool|string
     */
    protected function getRedirectPage($pageRedirect)
    {
        if (!$pageRedirect->getId()) {
            return false;
        }
        $cmsHomePage = $this->helper->getCmsHomePage();
        if ($pageRedirect->getIdentifier() == $cmsHomePage) {
            return '';
        }
        return $this->helper->buildUrl($pageRedirect->getIdentifier());
    }

    /**
     * @param $id
     * @return \Magento\Cms\Model\Page
     */
    protected function getCmsPageById($id)
    {
        return $this->pageFactory->create()->load($id);
    }
}
