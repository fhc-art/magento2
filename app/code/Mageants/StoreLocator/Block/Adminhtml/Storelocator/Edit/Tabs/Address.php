<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Storelocator\Edit\Tabs;

/**
 * Locator Address tab
 */
class Address extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * Current Store
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
     * Current CountryFactory
     *
     * @var \Magento\Directory\Model\Config\Source\Country
     */
	protected $_countryFactory;
    
	/**
     * @param \Magento\Backend\Block\Template\Context 
     * @param \Magento\Framework\Registry 
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param \Magento\Directory\Model\Config\Source\Country
	 * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) 
    {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_countryFactory = $countryFactory;
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
        $fieldset = $form->addFieldset('address_fieldset', ['legend' => __('Address Information')]);

        $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => true,
                'after_element_html'=>'<small>Enter Proper Address based on this your Latitude and Longitude count.</small>'
            ]
        );

        $fieldset->addField(
            'region',
            'text',
            [
                'name' => 'region',
                'label' => __('Region'),
                'title' => __('region'),
                'required' => false,
                'after_element_html'=>'<small>please Enter region name.</small>'
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('city'),
                'required' => true,
                'after_element_html'=>'<small>Please Enter city name.</small>'
            ]
        );
            
        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'title' => __('postcode'),
                'required' => false,
                'after_element_html'=>'<small>Enter postcode.</small>'
            ]
        ); 
        $optionsc=$this->_countryFactory->toOptionArray();
        $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('country'),
                'required' => true,
                'values' => $optionsc,
                'after_element_html'=>'<small>Select country of store.</small>'
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
		return parent::_prepareForm();
    }

    /**
     * get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Address');
    }

	/**
     * get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Address');
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
