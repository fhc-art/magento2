<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CreditLimit
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CreditLimit\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         *
         */
    	$installer = $setup;
        /**
         *
         */
    	$installer->startSetup();
    	 
    	
    	$table = $installer->getConnection()->newTable(
    			$installer->getTable('ced_creditlimit')
    	)
    	->addColumn(
    			'id',
    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    			null,
    			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    			'Id'
    	)
    	->addColumn(
    			'credit_amount',
    			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    			'12,4',
    			[],
    			'credit_amount'
    	)
    	->addColumn(
    			'used_amount',
    			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    			'12,4',
    			[],
    			'Used Amount'
    	)
    	->addColumn(
    			'remaining_amount',
    			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    			'12,4',
    			[],
    			'Remaining Amount'
    	)->addColumn(
    			'customer_id',
    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    			null,
    			['unsigned' => true, 'nullable' => false],
    			'Customer Id'
    	)->addColumn(
    			'customer_email',
    			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    			255,
    			['unsigned' => true, 'nullable' => false],
    			'Customer Email'
    	)
    	->setComment(
    			'Credit Limits'
    	);
    	$installer->getConnection()->createTable($table);
    
    	$connection = $installer->getConnection();

    	$connection->rawQuery("INSERT INTO `".$installer->getTable('sales_order_status')."` (`status`, `label`) VALUES
            ('ced_credit_limit', 'Order Placed By Credit Limit');");
      $connection->rawQuery("INSERT INTO `".$installer->getTable('sales_order_status_state')."` (`status`, `state`, `is_default`, `visible_on_front`) VALUES
            ('ced_credit_limit', 'new', 0, 1);");

	    $installer->endSetup();
    }
}
