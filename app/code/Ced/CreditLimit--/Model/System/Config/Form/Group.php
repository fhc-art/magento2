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
namespace Ced\CreditLimit\Model\System\Config\Form;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\CouldNotSaveException;
/**
 * @api
 * @since 100.0.2
 */

class Group extends \Magento\Framework\App\Config\Value
{
	
    /**
     * @var Json
     */
    private $serializer;

    /**
     * Serialized constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param Json|null $serializer
     */
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
    	\Magento\Framework\App\ResourceConnection $resourceConnection,	
        array $data = [],
        Json $serializer = null
    ) {
    	$this->resourceConnection = $resourceConnection;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            $this->setValue(empty($value) ? false : $this->serializer->unserialize($value));
        }
    }

    /**
     * @return $this
     */
    
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
        	$connection = $this->resourceConnection->getConnection();
        	$customertable = $this->resourceConnection->getTableName('customer_entity');
        	$creditLimitTable = $this->resourceConnection->getTableName('ced_creditlimit');
        	
        	$sql = $connection->select()->from($creditLimitTable,new \Zend_Db_Expr('customer_id'));
        	$result = $connection->fetchAll($sql);
        	$existingCustomerIds = array_column($result,'customer_id');
        	//$unique = array_map("unserialize", array_unique(array_map("serialize", $this->getValue())));
        	if($this->hasDuplicates(array_column($this->getValue(),'customer_group_id'))){
        	    throw new CouldNotSaveException(__('Duplicate Group Values'));
        	}
        	
        	foreach($this->getValue() as $customervalue){
	
        		if (isset($customervalue['customer_group_id']) && $customervalue['credit_limit']){
        			if(!empty($existingCustomerIds)){
        			$sql = $connection->select()
        							->from($customertable,new \Zend_Db_Expr('email AS customer_email,entity_id AS customer_id,'.$customervalue['credit_limit'].' AS credit_amount,'.$customervalue['credit_limit'].' AS remaining_amount, 0.00 AS used_amount'))
        							->where('group_id = '.$customervalue['customer_group_id']. ' AND entity_id NOT IN ('.(implode(',',$existingCustomerIds)).')');
        			}
        			else{
        				$sql = $connection->select()
        					->from($customertable,new \Zend_Db_Expr('email AS customer_email,entity_id AS customer_id,'.$customervalue['credit_limit'].' AS credit_amount,'.$customervalue['credit_limit'].' AS remaining_amount, 0.00 AS used_amount'))
        					->where('group_id = '.$customervalue['customer_group_id']);
        			}
        			$customerData = $connection->fetchAll($sql);     
        			
        			if(is_array($customerData) && count($customerData)>0)
        			$sql = $connection->insertMultiple($creditLimitTable,$customerData);	
        		}
        	}
        	$value = $this->getValue();
        	if(isset($value['__empty'])){
        		unset($value['__empty']);	
        	}
            $this->setValue($this->serializer->serialize($value));      
        }
        parent::beforeSave();
        return $this;
    }
    
    protected function hasDuplicates($array){
        return count($array) !== count(array_unique($array));
    }
}
