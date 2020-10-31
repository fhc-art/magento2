<?php


namespace Tbi\CustomerIntegration\Api;

interface AddCustomerManagementInterface
{

    /**
     * POST for AddCustomer api
     * @param string $param
     * @return string
     */
    public function postAddCustomer($param);
}
