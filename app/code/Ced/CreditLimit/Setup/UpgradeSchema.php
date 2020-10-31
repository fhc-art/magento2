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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Ced\CreditLimit\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('');

        if (version_compare($context->getVersion(), '0.0.3') < 0) {
	        if (!$setup->getConnection()->isTableExists($setup->getTable('ced_credit_limit_order'))) {
		        $table = $installer->getConnection()->newTable(
		    			$installer->getTable('ced_credit_limit_order')
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
	    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	    			null,
	    			['unsigned' => true, 'nullable' => false],
	    			'Order Id'
	    		)
	    		->addColumn(
	    			'order_amount',
	    			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
	    			'12,4',
	    			['unsigned' => true, 'nullable' => false],
	    			'Order Amount'
	    		)
	    		->addColumn(
	    			'credit_amount',
	    			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
	    			'12,4',
	    			['unsigned' => true, 'nullable' => false],
	    			'Credit Amount'
	    		)
	    		->setComment(
	    			'Credit Limits'
		    	);
		    	$installer->getConnection()->createTable($table);
		    }

		   if (!$setup->getConnection()->isTableExists($setup->getTable('ced_credit_limit_transaction'))) {
		        $table = $installer->getConnection()->newTable(
		    			$installer->getTable('ced_credit_limit_transaction')
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
	    			'transaction_id',
	    			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	    			255,
	    			['unsigned' => true, 'nullable' => false],
	    			'Transaction Id'
	    		)
	    		->addColumn(
	    			'amount_paid',
	    			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
	    			'12,4',
	    			['unsigned' => true, 'nullable' => false],
	    			'Amount Paid'
	    		)
	    		->setComment(
	    			'Credit Limits'
		    	);
		    	$installer->getConnection()->createTable($table);
		    }

		}
		if (version_compare($context->getVersion(), '0.0.4') < 0) {
			$tableName = $installer->getTable('ced_credit_limit_transaction');
			if ($installer->getConnection()->isTableExists($tableName) == true) {
				$installer->getConnection()->addColumn(
						$tableName,
						'created_at',
						[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
						'length' => null,
						'nullable' => false,
						'comment' => 'Created At'
						]
				);

			}
		}
		
		if (version_compare($context->getVersion(), '0.0.5') < 0) {
			$connection = $installer->getConnection();
			$connection->rawQuery("INSERT INTO `".$installer->getTable('sales_order_status')."` (`status`, `label`) VALUES
            ('shipped_pending_payment', 'Shipped,Pending Payment');");
			$connection->rawQuery("INSERT INTO `".$installer->getTable('sales_order_status_state')."` (`status`, `state`, `is_default`, `visible_on_front`) VALUES
            ('shipped_pending_payment', 'new', 0, 1);");
		}
		
		
		if (version_compare($context->getVersion(), '0.0.9') < 0) {
			$tableName = $installer->getTable('ced_creditlimit');
			if ($installer->getConnection()->isTableExists($tableName) == true) {
				$installer->getConnection()->addColumn(
						$tableName,
						'payment_due',
						[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						'length' => null,
						'nullable' => false,
						'comment' => 'Payment Due'
						]
				);
			
			}
		}
		
		$tableName = $installer->getTable('sales_order');
		if ($installer->getConnection()->isTableExists($tableName) == true) {
			$installer->getConnection()->addColumn(
					$tableName,
					'creditdue_order',
					[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'length' => 11,
					'nullable' => false,
					'comment' => 'Due Order'
					]
			);
				
		}
		$installer->endSetup();

    }
}
