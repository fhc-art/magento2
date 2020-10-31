<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use \Magento\Backend\Block\Widget\Form\Generic;
use MageWorx\CustomerPrices\Model\CustomerPrices as CustomerPricesModel;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices;

class Form extends Generic
{
    /**
     * @var string
     */
    protected $targetForm = 'customer_price_form';

    /**
     * @var CustomerPrices
     */
    protected $customerPriceResourceModel;

    /**
     * @var array
     */
    protected $customerGlobalPriceData;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CustomerPrices $customerPriceResourceModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CustomerPrices $customerPriceResourceModel,
        array $data = []
    ) {
        $this->customerPriceResourceModel = $customerPriceResourceModel;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('customerprices_global_');
        $form->setFieldNameSuffix('customerprices_global_price');

        $params = $this->getRequest()->getParams();

        if (!empty($params['id'])) {
            $customerId = $params['id'];
        } else {
            $customerId = null;
        }

        $fieldset = $form->addFieldset(
            'customerprices_global_price',
            ['legend' => __('Global Price:')]
        );

        $fieldset->addField(
            'global_price',
            'text',
            [
                'name'           => 'global_price',
                'title'          => __('Global Customer Price'),
                'label'          => __('Global Customer Price'),
                'note'           => $this->getGlobalPriceMessage(),
                'value'          => $this->getGlobalPriceValue($customerId),
                'data-form-part' => 'customer_form'
            ]
        );

        $fieldset->addField(
            'global_specail_price',
            'text',
            [
                'name'           => 'global_special_price',
                'title'          => __('Global Customer Special Price'),
                'label'          => __('Global Customer Special Price'),
                'note'           => $this->getGlobalSpecialPriceMessage(),
                'value'          => $this->getGlobalSpecialPriceValue($customerId),
                'data-form-part' => 'customer_form'
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param int $customerId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getGlobalPriceValue($customerId)
    {
        if (is_null($customerId)) {
            return '';
        }

        $this->customerGlobalPriceData = $this->customerPriceResourceModel->getProductDataByCustomerIdAndTypePrice(
            $customerId,
            CustomerPricesModel::TYPE_PRICE_СUSTOMER_GLOBAL
        );

        return !empty($this->customerGlobalPriceData['price']) ? $this->customerGlobalPriceData['price'] : '';

    }

    /**
     * @param int $customerId
     * @return string
     */
    protected function getGlobalSpecialPriceValue($customerId)
    {
        if (is_null($customerId)) {
            return '';
        }

        return !empty($this->customerGlobalPriceData['special_price']) ? $this->customerGlobalPriceData['special_price'] : '';
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getGlobalPriceMessage()
    {
        return __(
            'This price set the price for all your products for this customer. %1 '
            . 'Examples: %1 '
            . '%2 - replace price with given value %1 '
            . '%3 - increase/decrease current price by given value %1 '
            . '%4 - increase/decrease current price by given percent',
            '<br>','10.99','±10.99','±15%'
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getGlobalSpecialPriceMessage()
    {
        return __(
            'This price set the special price for all your products for this customer. %1 '
            . 'Examples: %1 '
            . '%2 - replace special price with given value %1 '
            . '%3 - increase/decrease special price by given value %1 '
            . '%4 - increase/decrease special price by given percent ',
            '<br>','10.99','±10.99','±15%'
        );
    }
}