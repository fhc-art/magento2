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
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab;
 
class Customer extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $moduleManager;
 
   
    protected $_gridFactory;
 
    protected $_objectManager;
    
 
    protected $backendHelper;
    
   // protected $_resource;
   
    protected $_status;
    
    protected $_coreRegistry;
   /**
    * 
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Backend\Helper\Data $backendHelper
    * @param \Magento\Framework\ObjectManagerInterface $objectManager
    * @param \Magento\Framework\App\ResourceConnection $resource
    * @param \Magento\Framework\Module\Manager $moduleManager
    * @param array $data
    */
    public function __construct(
    		
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
    //	\Magento\Framework\App\ResourceConnection $resource,
    		\Magento\Framework\Registry $registry,
    	//\Ced\TeamMember\Model\System\Config\Source\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
    	
      
        $this->_objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    //    $this->_resource = $resource;
        $this->_coreRegistry = $registry;
       // $this->_status = $status;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
          parent::_construct();
        $this->setId('customerGrid');
        $this->setDefaultSort('Asc');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        
    }
 
    /**
     * @return $this
     */
	protected function _prepareCollection()
    {
        $collection =  $this->_objectManager->create("Magento\Customer\Model\Customer")->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns() {
    	
    	$this->addColumn('customer_id', array(
    			//'type'      => 'radio',
    			'align'     => 'center',
    			'index'     => 'customer_id',
    			'renderer' => 'Ced\CreditLimit\Block\Adminhtml\CreditLimit\Renderer\Radiobox'
    			//'html_name'   =>'customer_id',
    			//'field_name'=>'customer_id',
    			//'checked'=>''
    	
    	));
		 $this->addColumn('entity_id', array(
            'header'    =>__('ID#'),
            'index'     =>'entity_id',
            'align'     => 'left',
            'width'    => '50px'
        ));
		 $this->addColumn('firstname', array(
		 		'header'    =>__('First Name'),
		 		'index'     =>'firstname',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));
		 $this->addColumn('lastname', array(
		 		'header'    =>__('Last Name'),
		 		'index'     =>'lastname',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));
		 $this->addColumn('email', array(
		 		'header'    =>__('Email'),
		 		'index'     =>'email',
		 		'align'     => 'left',
		 		'width'    => '50px',
		 		'html_name'   =>'email',
    			'field_name'=>'email',
		 ));
		
		
        return parent::_prepareColumns();
    }
	
	  public function getValues(){
	  	
	  	$arr = array();
	  	if($this->_coreRegistry->registry('credit_limit')){
	  		return $this->_coreRegistry->registry('credit_limit')->getCustomerId();
	  	}
	  	else
	  		return '';
	  }
	  public function getGridUrl(){
	  
	  
	  	return $this->getUrl('*/*/grid', array('_secure'=>true, '_current'=>true));
	  
	  }
}