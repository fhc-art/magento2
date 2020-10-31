<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Bss\LayerNavigation\Helper\Data as LayerHelper;
use Bss\LayerNavigation\Model\Config\Source\FilterType;
use Bss\LayerNavigation\Model\Config\Source\Expand;

class Layer extends \Magento\Catalog\Block\Adminhtml\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var FieldFactory
     */
    protected $fileldFactory;

    /**
     * @var LayerHelper
     */
    protected $layerHelper;

    /**
     * @var PropertyLocker
     */
    private $propertyLocker;

    /**
     * @var Expand
     */
    protected $expand;

    /**
     * @var FilterType
     */
    protected $filterType;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\Filter
     */
    protected $moduleFilter;

    /**
     * @var
     */
    protected $filterModel;

    /**
     * Layer constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FilterType $filterType
     * @param PropertyLocker $propertyLocker
     * @param FieldFactory $fieldFactory
     * @param \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter
     * @param Expand $expand
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FilterType $filterType,
        PropertyLocker $propertyLocker,
        FieldFactory $fieldFactory,
        \Bss\LayerNavigation\Model\Layer\Filter $moduleFilter,
        Expand $expand,
        array $data = []
    ) {
        $this->moduleFilter = $moduleFilter;
        $this->expand = $expand;
        $this->filterType = $filterType;
        $this->propertyLocker = $propertyLocker;
        $this->fileldFactory = $fieldFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('BSS Layered Navigation');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('BSS Layered Navigation');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return \Magento\Catalog\Block\Adminhtml\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');
        $this->getFilterModel()->prepareAttributeData($attributeObject);

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $expand = $this->expand->toOptionArray();

        $fieldset = $form->addFieldset('layer_fieldset', ['legend' => __('Bss Layered Navigation')]);

        array_unshift($expand, ['value' => 2, 'label' => __('Use General Setting')]);

        $fieldset->addField(
            LayerHelper::FIELD_FILTER_TYPE,
            'select',
            [
                'name'   => LayerHelper::FIELD_FILTER_TYPE,
                'label'  => __('Display Option Setting'),
                'title'  => __('Display Option Setting'),
                'class'  => 'layer_attribute_field',
                'values' => $this->filterType->toOptionArray()
            ]
        );

        $fieldset->addField(
            LayerHelper::FIELD_SHOWMORE,
            'select',
            [
                'name'   => LayerHelper::FIELD_SHOWMORE,
                'label'  => __('Show More Less'),
                'title'  => __('Show More Less'),
                'class'  => 'layer_attribute_field',
                'values' => [
                    ['value' => '1', 'label' => __('Use General Setting')],
                    ['value' => '0', 'label' => __('No')]
                    
                ]
            ]
        );

        $fieldset->addField(
            LayerHelper::FIELD_IS_EXPAND,
            'select',
            [
                'name'   => LayerHelper::FIELD_IS_EXPAND,
                'label'  => __('Expand/Collapse'),
                'title'  => __('Expand/Collapse'),
                'class'  => 'layer_attribute_field',
                'values' => $expand,
            ]
        );

        $refField = $this->fileldFactory->create(
            ['fieldData' => ['value' => '1,2', 'separator' => ','], 'fieldPrefix' => '']
        );
        $dependencies = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Form\Element\Dependence::class
        )->addFieldMap('is_filterable', 'is_filterable')
        ->addFieldMap(LayerHelper::FIELD_FILTER_TYPE, LayerHelper::FIELD_FILTER_TYPE)
        ->addFieldMap(LayerHelper::FIELD_IS_EXPAND, LayerHelper::FIELD_IS_EXPAND)
        ->addFieldMap(LayerHelper::FIELD_SHOWMORE, LayerHelper::FIELD_SHOWMORE)
        ->addFieldDependence(LayerHelper::FIELD_FILTER_TYPE, 'is_filterable', $refField)
        ->addFieldDependence(LayerHelper::FIELD_IS_EXPAND, 'is_filterable', $refField)
        ->addFieldDependence(LayerHelper::FIELD_SHOWMORE, 'is_filterable', $refField);

        $this->_eventManager->dispatch('product_attribute_form_build_layer_tab', [
            'form'         => $form,
            'attribute'    => $attributeObject,
            'dependencies' => $dependencies
        ]);

        $this->setChild('form_after', $dependencies);

        $this->setForm($form);
        $form->setValues($attributeObject->getData());
        $this->propertyLocker->lock($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->moduleFilter;
        }
        return $this->filterModel;
    }
}
