<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Storelocator\Edit\Tabs;

/**
 * Locator Coordinates tab
 */
class Coordinats extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        $fieldset = $form->addFieldset('coordinator_fieldset', ['legend' => __('Store Coordinates')]);

        $fieldset->addField(
            'latitude',
            'text',
            [
                'name' => 'latitude',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'after_element_html'=>'<small>Example for Silicon Roundabout, London: 51.525127</small>',
            ]
        );
        
        $fieldset->addField(
            'longitude',
            'text',
            [
                'name' => 'longitude',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'after_element_html'=>'<small>Example for Silicon Roundabout, London: -0.088302</small>',
            ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
 
	/**
	 * Get tab Label
	 *
	 * @return $string
	 */
    public function getTabLabel()
    {
        return __('Store Coordinates');
    }
	
	/**
	 * Get tab title
	 *
	 * @return $string
	 */
    public function getTabTitle()
    {
        return __('Store Coordinates');
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
	
	/**
	 * prepare form html
	 *
	 * @return $string
	 */
    public function getFormHtml()
    {
        $html=parent::getFormHtml();
        $html.=$this->setTemplate('Mageants_StoreLocator::map.phtml')->toHtml(); 
        return $html;
    }
}
