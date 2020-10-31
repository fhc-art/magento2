<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


declare(strict_types=1);

namespace Amasty\CustomerLogin\Setup\Operation;

use Amasty\CustomerLogin\Api\Data\LoggedInInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchemaTo110
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $loggedInTable = $setup->getTable(CreateCustomerLoginLogTable::TABLE_NAME);

        $setup->getConnection()->addColumn(
            $loggedInTable,
            LoggedInInterface::STORE_ID,
            [
                'type' => Table::TYPE_SMALLINT,
                'default' => null,
                'nullable' => true,
                'unsigned' => true,
                'comment' => 'Logged In Store ID'
            ]
        );
    }
}
