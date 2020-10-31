<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Module uninstall code
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $connection->dropTable($setup->getTable('mageworx_customerprices'));
        $connection->dropTable($setup->getTable('mageworx_catalog_product_entity_decimal_customer_prices'));
        $connection->dropTable($setup->getTable('mageworx_catalog_product_index_price'));
        $connection->dropTable($setup->getTable('mageworx_catalog_product_index_price_final_tmp'));
        $connection->dropTable($setup->getTable('mageworx_catalog_product_index_price_opt_agr_tmp'));
        $connection->dropTable($setup->getTable('mageworx_catalog_product_index_price_opt_tmp'));
        $connection->dropTable($setup->getTable('mageworx_catalogrule_product_price'));

        $setup->endSetup();
    }
}