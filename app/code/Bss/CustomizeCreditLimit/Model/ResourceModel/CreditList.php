<?php

namespace Bss\CustomizeCreditLimit\Model\ResourceModel;

class CreditList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('customize_ced_credit_list', 'id');
    }

    /**
     * @param string $tableName
     * @param array $data
     */
    public function insertData($tableName, $data)
    {
        try {
            $connection = $this->_resources->getConnection();
            $table = $connection->getTableName($tableName);
            if (!empty($data)) {
                $connection->insertMultiple($table, $data);
            }
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
        }
    }

    /**
     * @param string $tableName
     * @param array $bind
     * @param string $where
     */
    public function updateData($tableName, $bind, $where ='')
    {
        try {
            $connection = $this->_resources->getConnection();
            $table = $connection->getTableName($tableName);
            if (!empty($bind)) {
                $connection->update($table, $bind, $where);
            }
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
        }
    }
}