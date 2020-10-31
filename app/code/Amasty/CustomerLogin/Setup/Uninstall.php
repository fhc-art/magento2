<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


declare(strict_types=1);

namespace Amasty\CustomerLogin\Setup;

use Amasty\CustomerLogin\Setup\Operation\CreateCustomerLoginLogTable;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->removeTableAndConfig($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeTableAndConfig(SchemaSetupInterface $setup)
    {
        $defaultConnection = $setup->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $defaultConnection->dropTable($setup->getTable(CreateCustomerLoginLogTable::TABLE_NAME));
        $defaultConnection->delete(
            $setup->getTable('core_config_data'),
            "`path` LIKE 'amcustomerlogin/%'"
        );
    }
}
