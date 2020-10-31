<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Model;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Integration\Model\Oauth\TokenFactory;

class CustomerToken implements \Amasty\CustomerLogin\Api\CustomerTokenInterface
{
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(
        TokenFactory $tokenFactory,
        CustomerRepository $customerRepository
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    public function createCustomerToken($customerId)
    {
        /** TODO log */
        $customer = $this->customerRepository->getById($customerId);
        return $this->tokenFactory->create()->createCustomerToken($customer->getId())->getToken();
    }
}
