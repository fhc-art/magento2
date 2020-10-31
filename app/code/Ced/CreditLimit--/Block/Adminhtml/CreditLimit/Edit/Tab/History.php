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
 * Class History
 * @package Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab
 */
class History extends \Magento\Backend\Block\Widget\Grid\Extended
{
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
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
          parent::_construct();
        $this->setId('transactionGrid');
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
        $collection =  $this->_objectManager->create("Ced\CreditLimit\Model\Transaction")->getCollection()->addFieldToFilter('customer_id',$this->_objectManager->create('Ced\CreditLimit\Model\CreditLimit')->load($this->getRequest()->getParam('id'))->getCustomerId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns() {
    	
    	
		 $this->addColumn('id', array(
            'header'    =>__('ID#'),
            'index'     =>'id',
            'align'     => 'left',
            'width'    => '50px'
        ));
		 $this->addColumn('amount_paid', array(
		 		'header'    =>__('Paid Amount'),
		 		'index'     =>'amount_paid',
		 		'type'=>'currency',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));
		 $this->addColumn('transaction_id', array(
		 		'header'    =>__('Transaction_id'),
		 		'index'     =>'transaction_id',
		 		'align'     => 'left',
		 		'width'    => '50px'
		 ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
	  public function getGridUrl(){
	  	return $this->getUrl('*/*/transactionGrid', array('_secure'=>true, '_current'=>true));
	  }
}