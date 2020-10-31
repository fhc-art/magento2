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
namespace Bss\CatalogPermission\Observer\Category;

use Bss\CatalogPermission\Helper\ModuleConfig;
use Magento\Cms\Model\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Cms
 *
 * @package Bss\CatalogPermission\Observer\Category
 */
class Cms implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $response;
    /**
     * @var \Bss\CatalogPermission\Helper\ModuleConfig
     */
    protected $moduleConfig;
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Cms constructor.
     * @param Session $customerSession
     * @param ModuleConfig $moduleConfig
     * @param PageFactory $pageFactory
     * @param LayoutInterface $layout
     * @param Http $response
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Session $customerSession,
        ModuleConfig $moduleConfig,
        PageFactory $pageFactory,
        LayoutInterface $layout,
        Http $response,
        StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->response = $response;
        $this->storeManager = $storeManager;
        $this->moduleConfig = $moduleConfig;
        $this->pageFactory = $pageFactory;
        $this->layout = $layout;
    }

    /**
     * Observer Execute
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $enableCmsPagePermission = $this->moduleConfig->enableCmsPagePermission();
        if (!$enableCmsPagePermission) {
            return $this;
        }

        $pageId = $observer->getRequest()->getParams();
        $pageId = $pageId['page_id'];

        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $page = $this->pageFactory->create()->load($pageId);
        $data = $page->getData();
        $cmsPageIdRedirect = $this->moduleConfig->getPageIdToRedirectCms();
        $arrCustomerGroup = json_decode($data['bss_customer_group']);
        $message = $this->moduleConfig->getErrorMessageCms();
        if (is_array($arrCustomerGroup) && in_array($customerGroupId, $arrCustomerGroup)
            && ($pageId == $cmsPageIdRedirect)) {
            $messageBlock = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class)
                ->setTemplate('Bss_CatalogPermission::message.phtml')
                ->setMessage($message);
            $messageContainer = $this->layout->getBlock('bss_cms_page');
            $messageContainer->setChild('permission.message', $messageBlock);
        }
        return $this;
    }
}
