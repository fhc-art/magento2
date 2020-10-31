<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Controller\Index;

use Amasty\CustomerLogin\Model\LoggedInRepository;
use Amasty\CustomerLogin\Model\ResourceModel\LoggedIn\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Index extends Action
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LoggedInRepository
     */
    private $repository;

    /**
     * @var Session\Proxy
     */
    private $customerSession;

    /**
     * @var Url
     */
    private $customerUrl;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        LoggedInRepository $repository,
        Session\Proxy $customerSession,
        Url $customerUrl
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->repository = $repository;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($token = $this->getRequest()->getParam('token')) {
            /** @var \Amasty\CustomerLogin\Model\ResourceModel\LoggedIn\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('main_table.secret_key', $token)
                ->setPageSize(1)
                ->setCurPage(1);
            if ($collection->count()) {
                /** @var \Amasty\CustomerLogin\Model\LoggedIn $loggedIn */
                $loggedIn = $collection->getFirstItem();
                $loggedIn->setSecretKey(null);
                try {
                    $this->repository->save($loggedIn);
                    $this->customerSession->loginById($loggedIn->getCustomerId());
                    if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                        $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
                    }

                    return $this->_redirect($this->customerUrl->getDashboardUrl());
                } catch (\Exception $e) {
                    null;
                }
            }
        }

        return $this->_redirect('/');
    }
}
