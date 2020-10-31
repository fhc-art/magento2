<?php
namespace Bss\CustomizeCreditLimit\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $quoteItemTable = $installer->getTable('quote_item');

        $newQuoteItemColumns = [
            'invoice_credit_limit' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Invoice Credit',
            ],
            'order_credit_limit' =>[
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Order Credit',
            ]

        ];

        $connection = $installer->getConnection();
        //add new quote item columns
        foreach ($newQuoteItemColumns as $name => $definition) {
            $connection->addColumn($quoteItemTable, $name, $definition);
        }
        if ($setup->getConnection()->isTableExists($setup->getTable('ced_credit_limit_order'))) {
            $creditTable = $installer->getTable('ced_credit_limit_order');
            $newCreditColumns = [
                'check_paid_order' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Check Paid Order',
                ],

            ];
            // add new credit columns
            foreach ($newCreditColumns as $name => $definition) {
                $connection->addColumn($creditTable, $name, $definition);
            }
        }
        if (!$setup->getConnection()->isTableExists($setup->getTable('customize_ced_credit_list'))) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('customize_ced_credit_list')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id'
                )
                ->addColumn(
                    'order_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Order Id'
                )
                ->addColumn(
                    'quote_order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'Quote ,Order Id'
                )
                ->addColumn(
                    'invoice',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'Invoice'
                )
                ->addColumn(
                    'purchase',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true, 'nullable' => true],
                    'Purchase Field'
                )
                ->addColumn(
                    'date_paid',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Date Paid'
                )
                ->addColumn(
                    'order_amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['unsigned' => true, 'nullable' => false],
                    'Pay Amount'
                )->addIndex(
                    $setup->getIdxName('customize_ced_credit_list', ['id']),
                    ['id']
                );
            $installer->getConnection()->createTable($table);
            $connection->addForeignKey(
                $installer->getFkName(
                    $installer->getTable('customize_ced_credit_list'),
                    'customer_id',
                    $installer->getTable('ced_creditlimit'),
                    'customer_id'
                ),
                $installer->getTable('customize_ced_credit_list'),
                'customer_id',
                $installer->getTable('ced_creditlimit'),
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }
        $installer->endSetup();
    }
}
