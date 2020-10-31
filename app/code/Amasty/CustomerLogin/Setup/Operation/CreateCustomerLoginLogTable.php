<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Amasty\CustomerLogin\Api\Data\LoggedInInterface;

class CreateCustomerLoginLogTable
{
    const TABLE_NAME = 'amasty_customer_login_log';

    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     */
    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Admin Login as Customer Log table'
            )->addColumn(
                LoggedInInterface::LOGGEDIN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ],
                'Log Id'
            )->addColumn(
                LoggedInInterface::LOGGEDIN_TIME,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT
                ],
                'Login date'
            )->addColumn(
                LoggedInInterface::ADMIN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Logged In Admin ID'
            )->addColumn(
                LoggedInInterface::ADMIN_USERNAME,
                Table::TYPE_TEXT,
                40,
                [],
                'Logged In Admin Username'
            )->addColumn(
                LoggedInInterface::ADMIN_EMAIL,
                Table::TYPE_TEXT,
                128,
                [],
                'Logged In Admin Email'
            )->addColumn(
                LoggedInInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Logged In Customer ID'
            )->addColumn(
                LoggedInInterface::CUSTOMER_NAME,
                Table::TYPE_TEXT,
                255,
                [],
                'Logged In Customer Name'
            )->addColumn(
                LoggedInInterface::CUSTOMER_LASTNAME,
                Table::TYPE_TEXT,
                255,
                [],
                'Logged In Customer LastName'
            )->addColumn(
                LoggedInInterface::CUSTOMER_EMAIL,
                Table::TYPE_TEXT,
                255,
                [],
                'Logged In Customer Email'
            )->addColumn(
                LoggedInInterface::WEBSITE_ID,
                Table::TYPE_SMALLINT,
                255,
                ['unsigned' => true, 'nullable' => true],
                'Logged In Website ID'
            )->addColumn(
                LoggedInInterface::WEBSITE_CODE,
                Table::TYPE_TEXT,
                32,
                [],
                'Logged In Website Code'
            )->addColumn(
                LoggedInInterface::SECRET_KEY,
                Table::TYPE_TEXT,
                32,
                [],
                'Logged In Secret Key'
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    LoggedInInterface::ADMIN_ID,
                    $setup->getTable('admin_user'),
                    'user_id'
                ),
                LoggedInInterface::ADMIN_ID,
                $setup->getTable('admin_user'),
                'user_id',
                Table::ACTION_SET_NULL
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    LoggedInInterface::CUSTOMER_ID,
                    $setup->getTable('customer_entity'),
                    'entity_id'
                ),
                LoggedInInterface::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_SET_NULL
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    LoggedInInterface::WEBSITE_ID,
                    $setup->getTable('store_website'),
                    'website_id'
                ),
                LoggedInInterface::WEBSITE_ID,
                $setup->getTable('store_website'),
                'website_id',
                Table::ACTION_SET_NULL
            );
    }
}
