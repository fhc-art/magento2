<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StorePickup\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), "2.0.4", "<")) {
            $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'pickup_date',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Pickup Date',
            ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('quote_address'),
                'pickup_store',
                [
                    'type' => 'text',
                    'nullable' => false,
                    'comment' => 'Pickup Store',
                ]
            );
            }
        $installer->endSetup();
    }
}