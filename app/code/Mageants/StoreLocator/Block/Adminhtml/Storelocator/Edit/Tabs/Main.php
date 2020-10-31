<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Storelocator\Edit\Tabs;

/**
 * Locator Main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * Current Store
	 *
	 * @var \Magento\Store\Model\System\Store
	 */
    protected $_systemStore;

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
        \Mageants\StoreLocator\Helper\Data $helper,
        array $data = []
    ) 
    {
        $this->_systemStore = $systemStore;
        $this->_helper = $helper;
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
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form', 
                            'enctype' => 'multipart/form-data', 
                            'action' => $this->getData('action'), 
                            'method' => 'POST'
                        ]
            ]
        );
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Information')]);

        if ($model->getId()) {
            $fieldset->addField('store_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'sname',
            'text',
            [
                'name' => 'sname',
                'id' => 'pname',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'after_element_html'=>'<small>Enter Store full name.</small>'
            ]
        );
        
        $fieldset->addField(
            'sstatus',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'sstatus',
                'id' => 'pstatus',
                'required' => false,
                'options' => ['Enabled' => __('Enabled'), 'Disabled' => __('Disabled')],
            ]
        );
        
        $store_id=$this->_helper->getStoreList();
        $fieldset->addField(
           'storeId',
           'multiselect',
           [
             'name'     => 'storeId',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $store_id,
           ]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'title' => __('image'),
                'label' => __('Store Image'),
                'name' => 'image',
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );

        $fieldset->addField(
            'icon',
            'image',
            [
                'title' => __('icon'),
                'label' => __('Google Map Icon '),
                'name' => 'icon',
                'note' => 'Allow image type: icon'
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'title' => __('Position'),
                'label' => __('Position'),
                'name' => 'position',
                'class' => 'validate-zero-or-greater integer',
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
        return __('Information');
    }

    /**
     * Prepare title for tab
     *
     * @return $string
     */
    public function getTabTitle()
    {
        return __('Information');
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
