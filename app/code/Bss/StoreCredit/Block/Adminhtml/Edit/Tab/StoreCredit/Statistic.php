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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Block\Adminhtml\Edit\Tab\StoreCredit;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Bss\StoreCredit\Model\ResourceModel\Credit\CollectionFactory as CreditCollectionFactory;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

/**
 * Class Statistic
 * @package Bss\StoreCredit\Block\Adminhtml\Edit\Tab\StoreCredit
 */
class Statistic extends Extended
{
    /**
     * @var \Bss\StoreCredit\Model\ResourceModel\Credit\CollectionFactory
     */
    private $creditFactory;

    /**
     * Website collection
     *
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    private $websitesFactory;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param CreditCollectionFactory $creditFactory
     * @param CollectionFactory $websitesFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CreditCollectionFactory $creditFactory,
        CollectionFactory $websitesFactory,
        array $data = []
    ) {
        $this->creditFactory = $creditFactory;
        $this->websitesFactory = $websitesFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('bss_storecredit_tab_credit_statistic');
        $this->setDefaultSort('update_time');
        $this->setUseAjax(false);
    }

    /**
     * @return $this
     */
    public function _prepareCollection()
    {
        $collection = $this->creditFactory->create()
            ->addFieldToFilter(
                'customer_id',
                $this->getRequest()->getParam('id')
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function _prepareColumns()
    {
        $baseCurrencyCode = $this->_storeManager->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();
        $this->addColumn(
            'balance_amount',
            [
                'header' => __('Current Balance'),
                'width' => '50px',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => $baseCurrencyCode,
                'index' => 'balance_amount'
            ]
        );

        $this->addColumn(
            'website_id',
            [
                'header' => __('Website'),
                'width' => '50px',
                'align' => 'right',
                'type' => 'options',
                'index' => 'website_id',
                'options' => $this->websitesFactory->create()->toOptionHash()
            ]
        );
        $this->setFilterVisibility(false);
        $this->setSortable(false);
        $this->setEmptyText(__('There are no items.'));
        return parent::_prepareColumns();
    }

    /**
     * Remove row url
     *
     * @param \Magento\Framework\DataObject $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
