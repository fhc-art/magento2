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
 * @category   BSS
 * @package    Bss_B2bCustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Catalog\Model\Entity\Attribute;

class Front extends \Bss\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab\Front
{
    /**
     * {@inheritdoc}
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $yesnoSource = $this->yesNo->toOptionArray();
        $enableDisable = $this->enableDisable->toOptionArray();
        $fieldset = $form->addFieldset(
            'front_fieldset',
            ['legend' => __('Display Properties'), 'collapsable' => $this->getRequest()->has('popup')]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order', 'label' => __('Sort Order'),
                'title' => __('Sort Order'), 'class' => 'validate-digits',
                'note' => __('The order to display attribute on the frontend'),
            ]
        );
        $fieldset->addField(
            'is_visible',
            'select',
            [
                'name' => 'is_visible', 'label' => __('Status'),
                'title' => __('Status'), 'values' => $enableDisable,
                'value' => '1',
            ]
        );
        $usedInForms = $attributeObject->getUsedInForms();
        $showOnRegistration = $this->checkShowAttribute($attributeObject, $usedInForms, 'customer_account_create_frontend');
        $fieldset->addField(
            'customer_account_create_frontend',
            'select',
            [
                'name' => 'customer_account_create_frontend', 'label' => __('Display in Registration Form'),
                'title' => __('Display in Registration Form'), 'values' => $yesnoSource, 'value' => $showOnRegistration,
            ]
        );
        $showAccountEdit = $this->checkShowAttribute($attributeObject, $usedInForms, 'customer_account_edit_frontend');
        $fieldset->addField(
            'customer_account_edit_frontend',
            'select',
            [
                'name' => 'customer_account_edit_frontend', 'label' => __('Display in My Account Page'),
                'title' => __('Display in My Account Page'), 'values' => $yesnoSource, 'value' => $showAccountEdit,
            ]
        );
        $showOnBbRegistration = $this->checkShowAttribute($attributeObject, $usedInForms, 'b2b_account_create');
        $fieldset->addField(
            'b2b_account_create',
            'select',
            [
                'name' => 'b2b_account_create',
                'label' => __('Display in B2B Registration Form'),
                'title' => __('Display in B2B Registration Form'),
                'values' => $yesnoSource,
                'value' => $showOnBbRegistration,
            ]
        );
        $showOnBbRegistration = $this->checkShowAttribute($attributeObject, $usedInForms, 'b2b_account_edit');
        $fieldset->addField(
            'b2b_account_edit',
            'select',
            [
                'name' => 'b2b_account_edit',
                'label' => __('Display in B2B Account page'),
                'title' => __('Display in B2B Account page'),
                'values' => $yesnoSource,
                'value' => $showOnBbRegistration,
            ]
        );
        $showOrderDeltail = $this->checkShowAttribute($attributeObject, $usedInForms, 'order_detail');
        $fieldset->addField(
            'order_detail',
            'select',
            [
                'name' => 'order_detail', 'label' => __('Display in Order Detail Page'),
                'title' => __('Display in Order Detail Page'), 'values' => $yesnoSource, 'value' => $showOrderDeltail,
            ]
        );
        $fieldset->addField(
            'is_used_in_grid',
            'select',
            [
                'name' => 'is_used_in_grid', 'label' => __('Display in Customer Grid'),
                'title' => __('Display in Customer Grid'), 'values' => $yesnoSource,
                'value' => $attributeObject->getIsUsedInGrid(),
            ]
        );
        $showInEmail = $this->checkShowAttribute($attributeObject, $usedInForms, 'show_in_email');
        $fieldset->addField(
            'show_in_email',
            'select',
            [
                'name' => 'show_in_email', 'label' => __(' Add to Order Confirmation Email'),
                'title' => __(' Add to Order Confirmation Email'), 'values' => $yesnoSource, 'value' => $showInEmail,
            ]
        );
        $showInEmailNewAccount = $this->checkShowAttribute($attributeObject, $usedInForms, 'show_in_email_new_account');
        $fieldset->addField(
            'show_in_email_new_account',
            'select',
            [
                'name' => 'show_in_email_new_account', 'label' => __('Add to New Account Email'),
                'title' => __('Add to New Account Email'), 'values' => $yesnoSource, 'value' => $showInEmailNewAccount,
            ]
        );
        $showOrderFrontend = $this->checkShowAttribute($attributeObject, $usedInForms, 'show_order_frontend');
        $fieldset->addField(
            'show_order_frontend',
            'select',
            [
                'name' => 'show_order_frontend', 'label' => __('Add to Order Frontend'),
                'title' => __('Add to Order Frontend'), 'values' => $yesnoSource, 'value' => $showOrderFrontend,
            ]
        );
        $this->setForm($form);
        $this->propertyLocker->lock($form);
    }

    /**
     * @param $attributeObject
     * @param $usedInForms
     * @return int
     */
    private function checkShowAttribute($attributeObject, $usedInForms, $attributeCode)
    {
        if ($attributeObject->getAttributeId()) {
            if (in_array($attributeCode, $usedInForms)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    /**
     * Initialize form fileds values
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        $data = $this->getAttributeObject()->getData();
        if(isset($data['sort_order']))
            $data['sort_order'] = $data['sort_order'] - \Bss\CustomerAttributes\Helper\Data::DEFAULT_SORT_ORDER;
        $this->getForm()->addValues($data);
        return parent::_initFormValues();
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return mixed
     */
    private function getAttributeObject()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }
}
