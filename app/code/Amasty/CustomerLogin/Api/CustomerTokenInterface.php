<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Api;

interface CustomerTokenInterface
{
    /**
     * Create access token for admin given the customer credentials.
     *
     * @param int $customerId
     *
     * @return string
     */
    public function createCustomerToken($customerId);
}
