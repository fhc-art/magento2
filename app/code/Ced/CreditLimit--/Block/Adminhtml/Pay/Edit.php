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
namespace Ced\CreditLimit\Block\Adminhtml\Pay;
 
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
    		\Magento\Backend\Block\Widget\Context $context,
    		\Magento\Framework\ObjectManagerInterface $objectManager,
    		\Magento\Framework\Registry $registry,
    		array $data = []
    ) {
    	$this->_objectManager = $objectManager;
    	$this->_coreRegistry = $registry;
    	parent::__construct($context, $data);
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {
    	$this->_objectId = 'id';
    	$this->_blockGroup = 'ced_creditLimit';
    	$this->_controller = 'adminhtml_pay';
    
    	parent::_construct();
    
    	$this->buttonList->remove('delete');
    	if ($this->getRequest()->getParam('popup')) {
    		$this->buttonList->remove('back');
    		
    		if ($this->getRequest()->getParam('product_tab') != 'variations') {
    			$this->addButton(
    					'close',
    				[
    				'label'     => __('Close Window'),
    						'class'     => 'cancel',
    						'onclick'   => 'window.close()',
    						'level'     => -1
    				],
    				-100
    			);
    		}
    	}
    }
    
    /**
     * {@inheritdoc}
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
    	if ($this->getRequest()->getParam('popup')) {
    		$region = 'header';
    	}
    	parent::addButton($buttonId, $data, $level, $sortOrder, $region);
    }
}