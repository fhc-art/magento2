<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Storelocator\Edit\Tabs;

/**
 * Locator Contact tab
 */
class Contact extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * System Store
	 *
	 * @var \Magento\Store\Model\System\Store
	 */
	protected $_systemStore;
   
    /**
	 * Wysiwyg 
	 *
	 * @var \Magento\Cms\Model\Wysiwyg\Config
	 */
	protected $_wysiwygConfig;
	
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Magento\Framework\Registry
	 * @param \Magento\Framework\Data\FormFactory
	 * @param \Magento\Store\Model\System\Store
	 * @param \Magento\Cms\Model\Wysiwyg\Config
	 * @param array
	 */
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) 
    {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('locator_data');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('contact_fieldset', ['legend' => __('Contact Information')]);
        $fieldset->addField(
            'link',
            'text',
            [
                'name' => 'link',
                'label' => __('Website'),
                'title' => __('website'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html'=>'<small>Enter Store Website.</small>'
            ]
        );
        
        $fieldset->addField(
            'storeurl',
            'text',
            [
                'name' => 'storeurl',
                'label' => __('Store Url'),
                'title' => __('Store Url'),
                'required' => true,
                'after_element_html'=>'<small>Enter Store Url which is use for Front Display.</small>'
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'title' => __('phone'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html'=>'<small>Enter Store phone Number.</small>'
            ]
        );
        
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email-Id'),
                'title' => __('email'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'class'    => 'validate-email',
                'after_element_html'=>'<small>Enter Store Email-Id.</small>'
            ]
        ); 
        
		$form->setValues($model->getData());
        $this->setForm($form);
		return parent::_prepareForm();
    }
	
	/**
	 * Get Tab Label
	 *
	 * @return String
	 */
    public function getTabLabel()
    {
        return __('Contact');
    }
    
	/**
	 * Get Label Title
	 *
	 * @return String
	 */
    public function getTabTitle()
    {
        return __('Contact');
    }

	/**
	 * {@inheritdoc}
	 */
    public function canShowTab()
    {
        return true;
    }

	/**
	 * {@inheritdoc}
	 */
    public function isHidden()
    {
        return false;
    }
	
	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 * @return bool
	 */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
