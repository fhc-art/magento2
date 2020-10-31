<?php


namespace Tbi\CustomerIntegration\Model;

class GetCustomerManagement implements \Tbi\CustomerIntegration\Api\GetCustomerManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function getGetCustomer($param)
    {
        return 'hello api GET return the $param ' . $param;
    }
}
