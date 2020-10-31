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
class Schedule extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Mageants\StoreLocator\Helper\Data $helper,
        array $data = []
    ) 
    {
        $this->_helper=$helper;
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
        
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('monday_fieldset', ['legend' => __('Monday')]);
        $open=$this->_helper->getOpen();
       
        $fieldset->addField('mon_open', 'select', 
            [
                    'name' => 'mon_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('mon_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'mon_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('mon_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'mon_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('mon_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'mon_betime'
        ));

        $fieldset->addField('mon_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'mon_ctime'
        ));
        
        /*$fieldset->addField('apply_all', 'button', array(
          'label'     => '',
          'required'  => false,
          'value'  => 'Apply To All',
          'values'  => 'Apply To All',
          'tabindex' => 1,
          'name'=> 'Apply All'
        ));*/

        $fieldset = $form->addFieldset('tuesday_fieldset', ['legend' => __('Tuesday')]);
        $fieldset->addField('tue_open', 'select', 
            [
                    'name' => 'tue_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('tue_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'tue_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('tue_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'tue_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('tue_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'tue_betime'
        ));

        $fieldset->addField('tue_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'tue_ctime'
        ));


        $fieldset = $form->addFieldset('Wednesday_fieldset', ['legend' => __('Wednesday')]);
        $fieldset->addField('wed_open', 'select', 
            [
                    'name' => 'wed_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('wed_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'wed_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('wed_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'wed_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('wed_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'wed_betime'
        ));

        $fieldset->addField('wed_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'wed_ctime'
        ));

        
        $fieldset = $form->addFieldset('thursday_fieldset', ['legend' => __('Thursday')]);
        $fieldset->addField('thu_open', 'select', 
            [
                    'name' => 'thu_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('thu_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'thu_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('thu_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'thu_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('thu_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'thu_betime'
        ));

        $fieldset->addField('thu_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'thu_ctime'
        ));

        $fieldset = $form->addFieldset('friday_fieldset', ['legend' => __('Friday')]);
        $fieldset->addField('fri_open', 'select', 
            [
                    'name' => 'fri_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('fri_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'fri_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('fri_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'fri_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('fri_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'fri_betime'
        ));

        $fieldset->addField('fri_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'fri_ctime'
        ));

        $fieldset = $form->addFieldset('saturday_fieldset', ['legend' => __('Saturday')]);
        $fieldset->addField('sat_open', 'select', 
            [
                    'name' => 'sat_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('sat_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'sat_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('sat_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'sat_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('sat_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'sat_betime'
        ));

        $fieldset->addField('sat_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'sat_ctime'
        ));


        $fieldset = $form->addFieldset('sunday_fieldset', ['legend' => __('Sunday')]);
        $fieldset->addField('sun_open', 'select', 
            [
                    'name' => 'sun_open',
                    'label' => __('Open'),
                    'title' => __('Open'),
                    'required' => false,
                    'values' => $open,
            ]
        );

        $fieldset->addField('sun_otime', 'time', array(
          'required'  => true,
          'label'      => __('Opening Time'),
          'name'    => 'sun_otime',
          'value'  => '10,00',
          'tabindex' => 1
        ));

        $fieldset->addField('sun_bstime', 'time', array(
          'required'  => true,
          'label'      => __('Break Start Time'),
          'name'    => 'sun_bstime',
          'value'  => '14,00',
          'tabindex' => 1
        ));

        $fieldset->addField('sun_betime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Break End Time'),
          'value'  => '15,00',
          'name'    => 'sun_betime'
        ));

        $fieldset->addField('sun_ctime', 'time', array(
          'required'  => true,
          'tabindex' => 1,
          'label'      => __('Closing Time'),
          'value'  => '20,00',
          'name'    => 'sun_ctime'
        ));


        $form->addValues($model->getData());
        $this->setForm($form);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap("mon_open",'mon_open')
              ->addFieldMap("mon_otime",'mon_otime')
              ->addFieldMap("mon_bstime",'mon_bstime')
              ->addFieldMap("mon_betime",'mon_betime')
              ->addFieldMap("mon_ctime",'mon_ctime')
              ->addFieldMap("apply_all",'apply_all')
              ->addFieldDependence('mon_otime','mon_open','1')
              ->addFieldDependence('mon_bstime','mon_open','1')
              ->addFieldDependence('mon_betime','mon_open','1')
              ->addFieldDependence('mon_ctime','mon_open','1')
              ->addFieldDependence('apply_all','mon_open','1')
              
              ->addFieldMap("tue_open",'tue_open')
              ->addFieldMap("tue_otime",'tue_otime')
              ->addFieldMap("tue_bstime",'tue_bstime')
              ->addFieldMap("tue_betime",'tue_betime')
              ->addFieldMap("tue_ctime",'tue_ctime')
              ->addFieldDependence('tue_otime','tue_open','1')
              ->addFieldDependence('tue_bstime','tue_open','1')
              ->addFieldDependence('tue_betime','tue_open','1')
              ->addFieldDependence('tue_ctime','tue_open','1')
              
              ->addFieldMap("wed_open",'wed_open')
              ->addFieldMap("wed_otime",'wed_otime')
              ->addFieldMap("wed_bstime",'wed_bstime')
              ->addFieldMap("wed_betime",'wed_betime')
              ->addFieldMap("wed_ctime",'wed_ctime')
              ->addFieldDependence('wed_otime','wed_open','1')
              ->addFieldDependence('wed_bstime','wed_open','1')
              ->addFieldDependence('wed_betime','wed_open','1')
              ->addFieldDependence('wed_ctime','wed_open','1')

              ->addFieldMap("thu_open",'thu_open')
              ->addFieldMap("thu_otime",'thu_otime')
              ->addFieldMap("thu_bstime",'thu_bstime')
              ->addFieldMap("thu_betime",'thu_betime')
              ->addFieldMap("thu_ctime",'thu_ctime')
              ->addFieldDependence('thu_otime','thu_open','1')
              ->addFieldDependence('thu_bstime','thu_open','1')
              ->addFieldDependence('thu_betime','thu_open','1')
              ->addFieldDependence('thu_ctime','thu_open','1')

              ->addFieldMap("fri_open",'fri_open')
              ->addFieldMap("fri_otime",'fri_otime')
              ->addFieldMap("fri_bstime",'fri_bstime')
              ->addFieldMap("fri_betime",'fri_betime')
              ->addFieldMap("fri_ctime",'fri_ctime')
              ->addFieldDependence('fri_otime','fri_open','1')
              ->addFieldDependence('fri_bstime','fri_open','1')
              ->addFieldDependence('fri_betime','fri_open','1')
              ->addFieldDependence('fri_ctime','fri_open','1')

              ->addFieldMap("sat_open",'sat_open')
              ->addFieldMap("sat_otime",'sat_otime')
              ->addFieldMap("sat_bstime",'sat_bstime')
              ->addFieldMap("sat_betime",'sat_betime')
              ->addFieldMap("sat_ctime",'sat_ctime')
              ->addFieldDependence('sat_otime','sat_open','1')
              ->addFieldDependence('sat_bstime','sat_open','1')
              ->addFieldDependence('sat_betime','sat_open','1')
              ->addFieldDependence('sat_ctime','sat_open','1')

              ->addFieldMap("sun_open",'sun_open')
              ->addFieldMap("sun_otime",'sun_otime')
              ->addFieldMap("sun_bstime",'sun_bstime')
              ->addFieldMap("sun_betime",'sun_betime')
              ->addFieldMap("sun_ctime",'sun_ctime')
              ->addFieldDependence('sun_otime','sun_open','1')
              ->addFieldDependence('sun_bstime','sun_open','1')
              ->addFieldDependence('sun_betime','sun_open','1')
              ->addFieldDependence('sun_ctime','sun_open','1')
        );

		return parent::_prepareForm();
    }
	
	/**
	 * Get Tab Label
	 *
	 * @return String
	 */
    public function getTabLabel()
    {
        return __('Store Schedule');
    }
    
	/**
	 * Get Label Title
	 *
	 * @return String
	 */
    public function getTabTitle()
    {
        return __('Store Schedule');
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
      $html.=$this->setTemplate('Mageants_StoreLocator::Schedule.phtml')->toHtml(); 
      return $html;
    }

    /*public function timeChecker()
    {
            
        return $html;
    }
*/
}
