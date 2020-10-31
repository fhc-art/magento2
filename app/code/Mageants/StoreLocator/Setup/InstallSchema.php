<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Store\Model\StoreManagerInterface;

/**
 * InstallSchema for create Table
 */
class InstallSchema implements InstallSchemaInterface
{
    protected $StoreManager;
    /**     * Init     *     * @param EavSetupFactory $eavSetupFactory     */
    public function __construct(StoreManagerInterface $StoreManager)
    {
        $this->StoreManager=$StoreManager;
    }


    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
          * Create Database Table
          */
        $installer = $setup;
        $installer->startSetup();

        $service_url = 'https://www.mageants.com/index.php/rock/register/live?ext_name=Mageants_StoreLocator&dom_name='.$this->StoreManager->getStore()->getBaseUrl();
        $curl = curl_init($service_url);

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION =>true,
            CURLOPT_ENCODING=>'',
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ));
        
        $curl_response = curl_exec($curl);
        curl_close($curl);

        if (!$installer->tableExists('manage_store')) {
            $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('manage_store')
            )->addColumn('store_id', Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                ), 'store_id')
            ->addColumn(
                'sname',
                Table::TYPE_TEXT,
                255,
                ['nullable'  => false,],
                'Store Name'
            )
            ->addColumn(
                'storeId',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'storeId'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'position'
            )
            ->addColumn(
                'address',
                Table::TYPE_TEXT,
                255,
                ['nullable'  => false,],
                'Store Address'
            )
            ->addColumn(
                'city',
                Table::TYPE_TEXT,
                255,
                [],
                'City'
            )
            ->addColumn(
                'country',
                Table::TYPE_TEXT,
                255,
                [],
                'Country'
            )
            ->addColumn(
                'postcode',
                Table::TYPE_TEXT,
                255,
                [],
                'Postcode'
            )
            ->addColumn(
                'region',
                Table::TYPE_TEXT,
                255,
                [],
                'Region'
            )
            ->addColumn(
                'region_id',
                Table::TYPE_INTEGER,
                4,
                [],
                'Region ID'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                25,
                [],
                'Region Code'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                [],
                'Email'
            )
            ->addColumn(
                'phone',
                Table::TYPE_TEXT,
                255,
                [],
                'Phone'
            )
            ->addColumn(
                'link',
                Table::TYPE_TEXT,
                255,
                [],
                'Link'
            )
            ->addColumn(
                'storeurl',
                Table::TYPE_TEXT,
                255,
                [],
                'storeurl'
            )
            ->addColumn(
                'image',
                Table::TYPE_TEXT,
                255,
                [],
                'Image'
            )
            ->addColumn(
                'icon',
                Table::TYPE_TEXT,
                255,
                [],
                'icon'
            )
            ->addColumn(
                'latitude',
                Table::TYPE_TEXT,
                255,
                [],
                'Latitude'
            )
            ->addColumn(
                'longitude',
                Table::TYPE_TEXT,
                255,
                [],
                'Longitude'
            )
            ->addColumn(
                'sstatus',
                Table::TYPE_TEXT,
                10,
                [
                    'nullable'  => false,
                    'default'   => 'Enable',
                ],
                'Status'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Update at'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->addColumn('mon_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('mon_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('mon_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('mon_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('mon_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')

            ->addColumn('tue_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('tue_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('tue_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('tue_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('tue_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')

            ->addColumn('wed_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('wed_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('wed_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('wed_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('wed_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')

            ->addColumn('thu_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('thu_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('thu_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('thu_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('thu_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')

            ->addColumn('fri_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('fri_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('fri_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('fri_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('fri_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')

            ->addColumn('sat_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('sat_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('sat_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('sat_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('sat_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')

            ->addColumn('sun_open', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open')
            ->addColumn('sun_otime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Open Time')
            ->addColumn('sun_bstime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break Time')
            ->addColumn('sun_betime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Break end')
            ->addColumn('sun_ctime', Table::TYPE_TEXT, 10, ['nullable' => true, 'unsigned' => true], 'Store Close')
            ->addIndex(
                $setup->getIdxName(
                    $setup->getTable('manage_store'),
                    ['sname', 'address', 'city','country','postcode','region','email','phone','link'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['sname', 'address', 'city','country','postcode','region','email','phone','link'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'Mageants Manage Store Table'
            );
            $setup->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('store_product')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('store_product'))
                ->addColumn('store_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true])
                ->addColumn('product_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Magento Product Id')
                ->addForeignKey(
                    $installer->getFkName(
                        'manage_store',
                        'store_id',
                        'store_product',
                        'store_id'
                    ),
                    'store_id',
                    $installer->getTable('manage_store'),
                    'store_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'store_product',
                        'store_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Mageants Product Attachment relation table');

            $installer->getConnection()->createTable($table);
        }
        /*if (!$installer->tableExists('store_schedule')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('store_schedule'))


                ->addForeignKey(
                    $installer->getFkName(
                        'manage_store',
                        'store_id',
                        'store_schedule',
                        'store_id'
                    ),
                    'store_id',
                    $installer->getTable('manage_store'),
                    'store_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Mageants Product Attachment relation table');

            $installer->getConnection()->createTable($table);
        }*/
        $setup->endSetup();
    }
}
