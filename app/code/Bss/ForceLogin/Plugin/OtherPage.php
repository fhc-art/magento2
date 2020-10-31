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
 * @package    Bss_ForceLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ForceLogin\Plugin;

use Bss\ForceLogin\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

class OtherPage
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * OtherPage constructor.
     * @param Context $context
     * @param Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Context $context,
        Data $helperData,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->helperData = $helperData;
        $this->url = $context->getUrl();
        $this->messageManager = $context->getMessageManager();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->authSession = $authSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param \Magento\Framework\App\Action\Action $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function aroundDispatch(
        \Magento\Framework\App\Action\Action $subject,
        callable $proceed,
        RequestInterface $request
    ) {
        $result = $proceed($request);
        $resultPage = $result instanceof \Magento\Framework\View\Result\Page;
        $actionName = $request->getFullActionName();
        $ignoreList = $this->getIgnoreList();
        $enableLogin = $this->helperData->isEnable();
        $enableOtherPage = $this->helperData->isEnableOtherPage();
        $adminSession = $this->authSession->isLoggedIn();
        $customerLogin = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if (in_array($actionName, $ignoreList) || !$resultPage) {
            return $result;
        } elseif ($adminSession) {
            return $result;
        } elseif ($enableLogin && $enableOtherPage && !$customerLogin) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $message = $this->helperData->getAlertMessage();
            if ($message) {
                $this->messageManager->addErrorMessage($message);
            }
            return $resultRedirect->setPath('customer/account/login');
        } else {
            return $result;
        }
    }

    /**
     * Get IgnoreList
     * @return array
     */
    public function getIgnoreList()
    {
        $list = ['catalog_product_view','catalog_category_view','checkout_cart_index','checkout_index_index','search_term_popular',
            'catalogsearch_result_index','catalogsearch_advanced_index','cms_page_view','cms_noroute_index',
            'cms_index_index','customer_account_login', 'customer_account_loginPost','customer_account_logoutSuccess',
            'customer_account_logout','customer_account_resetPassword', 'customer_account_resetPasswordpost',
            'customer_account_index', 'customer_account_forgotpassword','customer_account_forgotpasswordpost',
            'customer_account_createPassword','customer_account_createpassword','customer_account_createPost',
            'adminhtml_index_index','adminhtml_noroute_index', 'adminhtml_auth_login','adminhtml_dashboard_index',
            'adminhtml_auth_logout', 'contact_index_index','customer_account_create', 'b2b_account_create'
        ];
        return $list;
    }
}
