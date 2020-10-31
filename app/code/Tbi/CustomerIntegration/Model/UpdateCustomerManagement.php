<?php


namespace Tbi\CustomerIntegration\Model;

class UpdateCustomerManagement implements \Tbi\CustomerIntegration\Api\UpdateCustomerManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function putUpdateCustomer($param)
    {
        return 'hello api PUT return the $param ' . $param;
    }
}
