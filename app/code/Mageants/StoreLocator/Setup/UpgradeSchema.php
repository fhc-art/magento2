<?php 
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
      if (version_compare($context->getVersion(), '1.0.1', '>')) {
            $setup->startSetup();
            $setup->getConnection()->addColumn(
                $setup->getTable('manage_store'),
                'region_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 4,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Region ID'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('manage_store'),
                'code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 4,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Region Code'
                ]
            );			
            $setup->endSetup();
        }
    }
}
