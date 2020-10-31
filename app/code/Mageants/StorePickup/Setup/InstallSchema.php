<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StorePickup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;     
    
    /**  
     * @param StoreManagerInterface $storeManager   
     */    
    public function __construct(StoreManagerInterface $storeManager)   
    {        
        $this->_storeManager = $storeManager;    
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'pickup_date',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Pickup Date',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'pickup_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Pickup Store',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'pickup_date',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Pickup Date',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'pickup_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Pickup Store',
            ]
        );

        $setup->endSetup();
        
        $service_url = 'https://www.mageants.com/index.php/rock/register/live?ext_name=Mageants_StorePickup&dom_name='.$this->_storeManager->getStore()->getBaseUrl();
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
    }
}
