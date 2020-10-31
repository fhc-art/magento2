<?php
namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit\Grid;
use Ced\CreditLimit\Model\ResourceModel\CreditLimit\CollectionFactory as CustomerCreditLmt;

/**
 * Class CreditlimitGrid
 * @package Ced\CreditLimit\Block\Adminhtml\CreditLimit\Grid
 */
class CreditlimitGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var CustomerCreditLmt
     */
    protected $customerCreditLmt;

    /**
     * CreditlimitGrid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CustomerCreditLmt $customerCreditLmt
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Backend\Helper\Data $backendHelper,
                                CustomerCreditLmt $customerCreditLmt,
                                array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->customerCreditLmt = $customerCreditLmt;
        $this->setData('area','adminhtml');
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('crd_lmt_grid');
        $this->setDefaultSort('id', 'desc');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $crdLmtCollection = $this->customerCreditLmt->create();
        $this->setCollection($crdLmtCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', ['header' => __('ID'), 'width' => '100', 'index' => 'id']);

        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer Email'),
                'sortable' => true,
                'index' => 'customer_email',
            ]
        );

        $this->addColumn(
            'credit_amount',
            [
                'header' => __('Credit Amount'),
                'sortable' => true,
                'index' => 'credit_amount',
            ]
        );

        $this->addColumn(
            'used_amount',
            [
                'header' => __('Used Credit Amount'),
                'sortable' => true,
                'index' => 'used_amount',
            ]
        );

        $this->addColumn(
            'remaining_amount',
            [
                'header' => __('Remaining Amount'),
                'sortable' => true,
                'index' => 'remaining_amount',
            ]
        );

        $this->addColumn('edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'climit/climit/edit'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]);

        $this->addColumn('payaction',
            [
                'header' => __('Pay Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Pay Amount'),
                        'url' => [
                            'base' => 'climit/climit/pay'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]);
        return parent::_prepareColumns();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareFilterButtons()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'action-secondary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-apply'])
        );
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('climit/climit/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
        return $this;
    }

    /**
     * @param $value
     * @param $row
     * @param $column
     * @param $isExport
     * @return string
     */
    public function formattedStatus($value, $row, $column, $isExport)
    {
        return ucfirst($value);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('climit/climit/crdlmtgrid', ['_current' => true]);
    }
}