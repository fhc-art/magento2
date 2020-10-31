<?php


namespace Tbi\CustomerIntegration\Model;

class DeleteCustomerManagement implements \Tbi\CustomerIntegration\Api\DeleteCustomerManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function deleteDeleteCustomer($param)
    {
        return 'hello api DELETE return the $param ' . $param;
    }
}
