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
namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ceditlimit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Credit Limit'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
       $this->addTab(
            'credit_limit',
            [
                'label' => __('Credit Limit'),
                'title' => __('Credit Limit'),
                'content' => $this->getLayout()->createBlock('Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab\Credit')->toHtml(),
                'active' => true
            ]
        );
       $this->addTab(
       		'customer_grid',
       		[
       		'label' => __('Customer'),
       		'title' => __('Customer'),
       		'content' => $this->getLayout()->createBlock('Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab\Customer')->toHtml(),
       		
       		]
       );
       
       if($this->getRequest()->getParam('id')){
       $this->addTab(
       		'balance_history',
       		[
       		'label' => __('Paid History'),
       		'title' => __('Paid History'),
       		'content' => $this->getLayout()->createBlock('Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab\History')->toHtml(),
       		 
       		]
       );
       }

        return parent::_beforeToHtml();
    }
}
