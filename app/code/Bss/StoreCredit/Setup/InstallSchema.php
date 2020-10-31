<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Bss\StoreCredit\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $installer = $setup;
            $installer->startSetup();

            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'base_bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Base Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'base_bss_storecredit_amount_input',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Base Store Credit Amount Input'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_invoice'),
                'base_bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Base Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_invoice'),
                'bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Amount'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_creditmemo'),
                'base_bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Base Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_creditmemo'),
                'bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_creditmemo'),
                'base_bss_storecredit_amount_refund',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Base Amount Refund'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_creditmemo'),
                'bss_storecredit_amount_refund',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Amount Refund'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'base_bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Base Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Amount'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('quote_address'),
                'base_bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Base Amount'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_address'),
                'bss_storecredit_amount',
                [
                    'type' => 'decimal',
                    'length' => '12,4',
                    'comment' => 'Store Credit Amount'
                ]
            );

            $balanceTable = $installer->getConnection()->newTable($installer->getTable('bss_storecredit_balance'))
                ->addColumn(
                    'balance_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Balance ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Customer Id'
                )
                ->addColumn(
                    'balance_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Balance Amount'
                )
                ->addColumn(
                    'website_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true],
                    'Website Id'
                )->addForeignKey(
                    $installer->getFkName('bss_storecredit_balance', 'website_id', 'store_website', 'website_id'),
                    'website_id',
                    $installer->getTable('store_website'),
                    'website_id',
                    Table::ACTION_SET_NULL
                )->addForeignKey(
                    $installer->getFkName('bss_storecredit_balance', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName('bss_storecredit_balance', ['customer_id']),
                    ['customer_id']
                )->addIndex(
                    $installer->getIdxName('bss_storecredit_balance', ['website_id']),
                    ['website_id']
                );
            $installer->getConnection()->createTable($balanceTable);

            $historyTable = $installer->getTable('bss_storecredit_balance_history');
            $balanceHistoryTable = $installer->getConnection()->newTable($historyTable)
                ->addColumn(
                    'history_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'History ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Balance ID'
                )
                ->addColumn(
                    'creditmemo_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Creditmemo ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Order ID'
                )
                ->addColumn(
                    'website_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true],
                    'Website ID'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true],
                    'Type'
                )
                ->addColumn(
                    'change_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Change Amount'
                )
                ->addColumn(
                    'balance_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Balance Amount'
                )
                ->addColumn(
                    'comment_content',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Comment Content'
                )
                ->addColumn(
                    'is_notified',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Is Notified'
                )
                ->addColumn(
                    'created_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Creation Time'
                )
                ->addColumn(
                    'updated_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated Time'
                )->addIndex(
                    $installer->getIdxName('bss_storecredit_balance_history', ['customer_id']),
                    ['customer_id']
                )->addIndex(
                    $installer->getIdxName('bss_storecredit_balance_history', ['is_notified']),
                    ['is_notified']
                )->addIndex(
                    $installer->getIdxName('bss_storecredit_balance_history', ['creditmemo_id']),
                    ['creditmemo_id']
                )->addForeignKey(
                    $installer->getFkName(
                        'bss_storecredit_balance_history',
                        'customer_id',
                        'bss_storecredit_balance',
                        'customer_id'
                    ),
                    'customer_id',
                    $installer->getTable('bss_storecredit_balance'),
                    'customer_id',
                    Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($balanceHistoryTable);
            $installer->endSetup();
        }
    }
}
