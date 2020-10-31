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

namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab;
/**
 * Class Customer
 * @package Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab
 */
 
class Customer extends \Magento\Backend\Block\Widget\Grid\Extended
{
	
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
    protected $_status;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
 	/**
 	 * 
 	 * @param \Magento\Backend\Block\Template\Context $context
 	 * @param \Magento\Backend\Helper\Data $backendHelper
 	 * @param \Magento\Framework\Registry $registry
 	 * @param \Ced\CreditLimit\Helper\Data $helper
 	 * @param \Ced\CreditLimit\Model\ResourceModel\CreditLimit\CollectionFactory $collectionFactory
 	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
 	 * @param \Ced\CreditLimit\Model\CreditLimit $creditLimit
 	 * @param array $data
 	 */
    public function __construct(
    		
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
    	\Magento\Framework\Registry $registry,
    	\Ced\CreditLimit\Helper\Data $helper,
    	\Ced\CreditLimit\Model\ResourceModel\CreditLimit\CollectionFactory $collectionFactory,
    	\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Ced\CreditLimit\Model\CreditLimit $creditLimit,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helper = $helper;
        $this->creditLimitCollection = $collectionFactory;
        $this->customerFactory = $customerFactory;
        $this->creditLimit = $creditLimit;
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
    	
    	if(!$this->getRequest()->getParam('id')){
            $customerIds = $this->creditLimitCollection->create()->getColumnValues('customer_id');
            if($customerIds)
            $collection =  $this->customerFactory->create()->addFieldToFilter('entity_id',['nin'=>$customerIds]);
            else
            $collection =  $this->customerFactory->create();
           
        }else{
            $creditLimit = $this->creditLimit->load($this->getRequest()->getParam('id'));
             $collection =  $this->customerFactory->create()->addFieldToFilter('entity_id',['eq'=>$creditLimit->getCustomerId()]);
         }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns() {
    	
    	if($this->helper->getAssignType()=='customer' && $this->_scopeConfig->getValue('b2bextension/credit_limit/assign_multiple_customer',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) && !$this->getRequest()->getParam('id')){
    		$this->addColumn('customer_id', array(
    				'type'      => 'checkbox',
    				'align'     => 'center',
    				'index'     => 'entity_id',
    				'field_name'     =>'customer_id[]',
    				'filter'=> false
    				 
    		));
    	}else{
    		$this->addColumn('customer_id', array(
    			'align'     => 'center',
    			'index'     => 'customer_id',
    			'renderer' => 'Ced\CreditLimit\Block\Adminhtml\CreditLimit\Renderer\Radiobox',
				'filter'=> false
    		));
    	}
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

    /**
     * @return string
     */
	  public function getValues(){
	  	
	  	$arr = array();
	  	if($this->_coreRegistry->registry('credit_limit')){
	  		return $this->_coreRegistry->registry('credit_limit')->getCustomerId();
	  	}
	  	else
	  		return '';
	  }

    /**
     * @return string
     */
	  public function getGridUrl(){
	  	return $this->getUrl('*/*/grid', array('_secure'=>true, '_current'=>true));
	  }
}
