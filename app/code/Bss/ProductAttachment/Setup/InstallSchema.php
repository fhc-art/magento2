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
 * @category   BSS
 * @package    Bss_ProductAttachment
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductAttachment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Install table bss_attachment_file
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('bss_productattachment_file')) {

            $fileTable = $installer->getConnection()->newTable($installer->getTable('bss_productattachment_file'))
                ->addColumn(
                    'file_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Title'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Description'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false],
                    'Status'
                )
                ->addColumn(
                    'type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false],
                    'Type'
                )
                ->addColumn(
                    'uploaded_file',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'File Name'
                )->addColumn(
                    'size',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Size'
                )->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Store View'
                )->addColumn(
                    'customer_group',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Customer Group'
                )->addColumn(
                    'limit_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Limit download'
                )->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Position'
                )->addColumn(
                    'downloaded_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Downloaded Time'
                )->addColumn(
                    'show_footer',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    0,
                    ['nullable' => false],
                    'Show Footer'
                );

            $installer->getConnection()->createTable($fileTable);
            $installer->getConnection()->addIndex(
                $installer->getTable('bss_productattachment_file'),
                $setup->getIdxName(
                    $installer->getTable('bss_productattachment_file'),
                    ['title', 'description'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title', 'description'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}
