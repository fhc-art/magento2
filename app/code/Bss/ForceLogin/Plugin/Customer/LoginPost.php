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
namespace Bss\ForceLogin\Plugin\Customer;

use Bss\ForceLogin\Helper\Data;
use Magento\Framework\App\Action\Context;

class LoginPost
{

    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * LoginPost constructor.
     * @param Context $context
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param $resultRedirect
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $resultRedirect)
    {
        $enable = $this->helperData->isEnable();
        if ($enable) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $configRedirectUrl = $this->getLoginRedirectUrl();
            return $resultRedirect->setPath($configRedirectUrl);
        } else {
            return $resultRedirect;
        }
    }

    /**
     * Bss Get Redirect
     * @return string
     */
    public function getLoginRedirectUrl()
    {
        $redirectToDashBoard = $this->helperData->isRedirectDashBoard();
        $currentUrl = $this->helperData->getSessionCatalog()->getBssCurrentUrl();
        $previousUrl = $this->helperData->getSessionCatalog()->getBssPreviousUrl();
        $this->helperData->getSessionCatalog()->unsBssCurrentUrl();
        $this->helperData->getSessionCatalog()->unsBssPreviousUrl();
        $configRedirectUrl = $this->helperData->getRedirectUrl();
        if ($configRedirectUrl == "home") {
            return "";
        } elseif ($configRedirectUrl == "previous") {
            if ($currentUrl) {
                return $currentUrl;
            } else {
                return $previousUrl;
            }
        } elseif ($configRedirectUrl == "customurl") {
            return $this->helperData->getCustomUrl();
        } elseif ($configRedirectUrl == "customer/account/index") {
            if ($redirectToDashBoard) {
                return $configRedirectUrl;
            } elseif ($currentUrl) {
                return $currentUrl;
            } else {
                return $previousUrl;
            }
        }
        return '';
    }
}
