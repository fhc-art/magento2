<?php
/**
 * @category   Mageants CMSImportExport
 * @package    Mageants_CMSImportExport
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreLocator\Block\Adminhtml\Store\Import;
/**
 * Form Class
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $urlBuider;

    /**
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
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Returns html for block import
     */
    public function getFormHtml()
    {
        $html=parent::getFormHtml();
        $html.=$this->setTemplate('Mageants_StoreLocator::Import/StoreImport.phtml')->toHtml(); 
        return $html;
    }

    /**
     * Returns ajax url for block import
     */
    public function getImportAjaxUrl(){
        return $this->urlBuilder->getUrl('storelocator/Storelocator/ajaximport');
    }
}
