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
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bRegistration\Observer;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class RedirectB2bUrl implements ObserverInterface
{
    /**
     * @var \Bss\B2bRegistration\Helper\Data
     */
    private $helper;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * RedirectB2bUrl constructor.
     *
     * @param \Bss\B2bRegistration\Helper\Data $helper
     * @param ResponseInterface $response
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Bss\B2bRegistration\Helper\Data $helper,
        ResponseInterface $response,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->response = $response;
        $this->storeManager = $storeManager;
    }

    /**
     * Redirect to B2b account craeate Page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $hasLastSlash = false;
        $enable = $this->helper->isEnable();
        if ($enable) {
            $request = $observer->getData('request');
            $bbUrl = $this->helper->getB2bUrl();
            $urlRequest = $request->getOriginalPathInfo();
            if (substr($urlRequest, -1) == '/') {
                $hasLastSlash = true;
            }
            $bbUrl = ltrim($bbUrl, '/');
            $bbUrl = rtrim($bbUrl, '/');
            $urlRequest = ltrim(rtrim($urlRequest, '/'), '/');
            if ($bbUrl == $urlRequest) {
                if ($hasLastSlash) {
                    $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                    $this->response->setRedirect($baseUrl . $urlRequest, 301)->sendResponse();
                }
                $controllerRequest = $observer->getData('controller_action')->getRequest();
                $controllerRequest->initForward();
                $controllerRequest->setModuleName('btwob');
                $controllerRequest->setControllerName('account');
                $controllerRequest->setActionName('create');
                $controllerRequest->setDispatched(false);
            }
        }
    }
}
