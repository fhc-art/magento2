<?php


namespace Tbi\CustomerIntegration\Model;

class AddCustomerManagement implements \Tbi\CustomerIntegration\Api\AddCustomerManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function postAddCustomer($param)
    {
        return 'hello api POST return the $param ' . $param;
    }
}
