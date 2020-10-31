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

namespace Ced\CreditLimit\Block\Adminhtml\Pay\Edit\Tab;


/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Balance extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Balance constructor.
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
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Manage Balance')]);
        $fieldset->addField(
        		'credit_amount',
        		'text',
        		[
        		'name' => 'credit_amount',
        		'label' => __('Total Limit'),
        		'title' => __('Total Limit'),
        		//'required' => true,
        		'readonly'=>true,
        		'class' 	=> 'validate-not-negative-number',
        		]
        );

        $fieldset->addField(
        		'remaining_amount',
        		'text',
        		[
        		'name' => 'remaining_amount',
        		'label' => __('Credit Balance'),
        		'title' => __('Credit Balance'),
        		//'required' => true,
        		'readonly'=>true,
        		'class' 	=> 'validate-not-negative-number',
        		]
        );
        $fieldset->addField(
        		'payment_due',
        		'text',
        		[
        		'name' => 'payment_due',
        		'label' => __('Payment Due'),
        		'title' => __('Payment Due'),
        		//'required' => true,
        		'readonly'=>true,
        		'class' 	=> 'validate-not-negative-number',
        		]
        );
        
       $fieldset->addField(
            'amount_paid',
            'text',
            [
                'name' => 'amount_paid',
                'label' => __('Paid Amount'),
                'title' => __('Paid Amount'),
                'required' => true,
        		'class' 	=> 'validate-not-negative-number',
            ]
        );
       $fieldset->addField(
       		'transaction_id',
       		'text',
       		[
       		'name' => 'transaction_id',
       		'label' => __('Transaction Id'),
       		'title' => __('Transaction Id'),
       		'required' => true,
       		]
       );
      	
       $fieldset->addField(
       		'created_at',
       		'date',
       		[
       		'name' => 'created_at',
       		'label' => __('Date'),
       		'date_format' => 'yyyy-MM-dd',
       		'required' => true,
       		]
       );
     
       $form->setValues($this->_coreRegistry->registry('credit_limit')->getData());
       $this->setForm($form);
        return $this;
    }
}
