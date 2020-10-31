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
namespace Ced\CreditLimit\Block\Adminhtml\Customer;
/**
 * Class Grid
 * @package Ced\CreditLimit\Block\Adminhtml\Customer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var
     */
    protected $_gridFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var
     */
    protected $backendHelper;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var
     */
    protected $_status;
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
    	\Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resource;
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
    	
    	$this->addColumn('test', array(
    			'type'      => 'radio',
    			'align'     => 'center',
    			'index'     => 'demo',
    			'html_name'   =>'demo',
    			'field_name'=>'demo[]',
    			'value'=>'1'
    	
    	));
		 $this->addColumn('entity_id', array(
            'header'    =>__('ID#'),
            'index'     =>'entity_id',
            'align'     => 'left',
            'width'    => '50px'
        ));
		 $this->addColumn('first_name', array(
		 		'header'    =>__('First Name'),
		 		'index'     =>'firstname',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));
		 $this->addColumn('last_name', array(
		 		'header'    =>__('Last Name'),
		 		'index'     =>'lastname',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));
		 $this->addColumn('email', array(
		 		'header'    =>__('Email'),
		 		'index'     =>'email',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));
		
		
        return parent::_prepareColumns();
    }

    /**
     * @return array
     */
	  public function getValues(){
	  	
	  	return array('1','2');
	  }
    
}