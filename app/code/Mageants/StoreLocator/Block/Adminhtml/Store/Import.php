<?php
/**
 * @category   Mageants CMSImportExport
 * @package    Mageants_CMSImportExport
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Store;

/**
 * Attachment Edit Form
 */
class Import extends \Magento\Backend\Block\Widget\Form\Container
{   
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array 
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) 
    {
		$this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize store post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'store_id';
        $this->_blockGroup = 'Mageants_StoreLocator';
        $this->_controller = 'adminhtml_store';
        parent::_construct();
        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->removeButton('save');
        $this->removeButton('reset');
    }

    protected function _buildFormClassName()
    {
        return  $this->nameBuilder->buildClassName(
            [$this->_blockGroup, 'Block', $this->_controller, 'Import', 'Form']
        );

    }
   
    public function getBackUrl()
    {
        return $this->getUrl('storelocator/storelocator/index');
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import Store');
    }
    
    /**
     * Prepare layout for RMA View
     */
    protected function _prepareLayout()
    {
		$this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
    
}
