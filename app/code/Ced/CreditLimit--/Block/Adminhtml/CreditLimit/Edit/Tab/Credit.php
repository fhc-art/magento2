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
 * Class Credit
 * @package Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit\Tab
 */
class Credit extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Credit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
	public function __construct(
	 \Magento\Backend\Block\Template\Context $context,
	 \Magento\Framework\Registry $registry,
	 \Magento\Framework\Data\FormFactory $formFactory,
	  array $data = []	
	)
	{
		parent::__construct($context, $registry, $formFactory, $data);
	}

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->_formFactory->create();
       
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Credit Limit')]);

       $fieldset->addField(
            'credit_amount',
            'text',
            [
                'name' => 'credit_amount',
                'label' => __('Credit Limit'),
                'title' => __('Credit Limit'),
                'required' => true,
        		'class' 	=> 'validate-not-negative-number',
            ]
        );
      if($this->getRequest()->getParam('id')){
      	
      	$fieldset->addField(
      			'used_amount',
      			'text',
      			[
      			'name' => 'used_amount',
      			'label' => __('Used Limit'),
      			'title' => __('Used Limit'),
      			//'required' => true,
      			'readonly'=>true,
      			'class' 	=> 'validate-text',
      			]
      	);

      	$fieldset->addField(
      			'remaining_amount',
      			'text',
      			[
      			'name' => 'remaining_amount',
      			'label' => __('Remaining Limit'),
      			'title' => __('Remaining Limit'),
      			//'required' => true,
      			'readonly'=>true,
      			'class' 	=> 'validate-text',
      			]
      	);
      }
       $form->setValues($this->_coreRegistry->registry('credit_limit')->getData());
       $this->setForm($form);
        return $this;
    }
}
