<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Block\Adminhtml\Customer\Edit;

use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices;
use Magento\Backend\Block\Template\Context;
use MageWorx\CustomerPrices\Model\Encoder;
use Magento\Backend\Block\Template;
use MageWorx\CustomerPrices\Model\CustomerPrices as CustomerPricesModel;

class CustomerPrice extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'customer/edit/customerprices/customers.phtml';

    /**
     * Block Grid
     */
    protected $blockGrid;

    /**
     * Block Form
     */
    protected $blockForm;

    /**
     * @var Encoder
     */
    protected $jsonEncoder;

    /**
     * @var CustomerPrices
     */
    protected $customerResourceModel;

    /**
     * CustomerPrice constructor.
     *
     * @param Context $context
     * @param Encoder $jsonEncoder
     * @param CustomerPrices $customerResourceModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Encoder $jsonEncoder,
        CustomerPrices $customerResourceModel,
        array $data = []
    ) {
        $this->jsonEncoder           = $jsonEncoder;
        $this->customerResourceModel = $customerResourceModel;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \MageWorx\CustomerPrices\Block\Adminhtml\Customer\Edit\Tab\Grid::class,
                'customer.products.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockForm()
    {
        if (null === $this->blockForm) {
            $this->blockForm = $this->getLayout()->createBlock(
                \MageWorx\CustomerPrices\Block\Adminhtml\Customer\Edit\Tab\Form::class,
                'customer.products.form'
            );
        }

        return $this->blockForm;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Customer Price');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
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
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormHtml()
    {
        return $this->getBlockForm()->toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsJson()
    {
        $params = $this->getRequest()->getParams();
        if (!empty($params['id'])) {
            $productIds         = $this->customerResourceModel->getProductIdsByCustomerId($params['id'], 'entity_id');
            $selectedProductIds = array_combine(array_values($productIds), array_values($productIds));
        } else {
            $selectedProductIds = [];
        }

        return $this->jsonEncoder->encode($selectedProductIds);
    }

    /**
     * example 5:{price:-10%,special_price:-20%}
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsPriceJson()
    {
        $params = $this->getRequest()->getParams();
        if (!empty($params['id'])) {
            $productPriceData = $this->customerResourceModel->getProductsPricesByCustomerId(
                $params['id'],
                CustomerPricesModel::TYPE_PRICE_CUSTOMER
            );
        } else {
            $productPriceData = [];
        }

        return $this->jsonEncoder->encode($productPriceData);
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
        return 'in_products';
    }

    /**
     * @return string
     */
    public function getFieldPriceId()
    {
        return 'in_custom_price_products';
    }
}