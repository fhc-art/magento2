<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Controller\Adminhtml\Customer;

use Amasty\CustomerLogin\Model\LoggedInFactory;
use Amasty\CustomerLogin\Model\LoggedInRepository;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;

class Login extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerLogin::admin_login';

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var Session
     */
    private $adminSession;

    /**
     * @var LoggedInFactory
     */
    private $loggedInFactory;

    /**
     * @var LoggedInRepository
     */
    private $loggedInRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Url
     */
    private $url;

    public function __construct(
        Action\Context $context,
        CustomerRepository $customerRepository,
        Session $adminSession,
        LoggedInFactory $loggedInFactory,
        LoggedInRepository $loggedInRepository,
        StoreManagerInterface $storeManager,
        Url $url
    ) {
        parent::__construct($context);
        $this->customerRepository = $customerRepository;
        $this->adminSession = $adminSession;
        $this->loggedInFactory = $loggedInFactory;
        $this->loggedInRepository = $loggedInRepository;
        $this->storeManager = $storeManager;
        $this->url = $url;
    }

    public function execute()
    {
        if ($customerId = (int)$this->getRequest()->getParam('customer_id')) {
            try {
                /** @var \Amasty\CustomerLogin\Model\LoggedIn $loggedIn */
                $loggedIn = $this->loggedInFactory->create();
                $customer = $this->customerRepository->getById($customerId);
                $adminUser = $this->adminSession->getUser();
                $websiteId = $this->getRequest()->getParam('website');

                if ($websiteId === null || !($website = $this->storeManager->getWebsite($websiteId))) {
                    $website = $this->storeManager->getWebsite($customer->getWebsiteId());
                }

                $storeId = $website->getDefaultStore()->getId();
                //phpcs:ignore
                mt_srand();
                //phpcs:ignore
                $secretKey = md5(mt_rand());
                $loggedIn->setCustomerId($customer->getId())
                    ->setCustomerEmail($customer->getEmail())
                    ->setCustomerName($customer->getFirstname())
                    ->setCustomerLastName($customer->getLastname())
                    ->setAdminId($adminUser->getId())
                    ->setAdminEmail($adminUser->getEmail())
                    ->setAdminUsername($adminUser->getUserName())
                    ->setWebsiteId($this->storeManager->getStore($storeId)->getWebsiteId())
                    ->setWebsiteCode($this->storeManager->getWebsite(
                        $this->storeManager->getStore($storeId)->getWebsiteId()
                    )->getCode())
                    ->setSecretKey($secretKey);
                $this->loggedInRepository->save($loggedIn);

                return $this->_redirect(
                    $this->url->setScope($storeId)->getUrl(
                        'amcustomerlogin/index/index',
                        ['token' => $secretKey, '_nosid' => true]
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong.'));
            }
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
