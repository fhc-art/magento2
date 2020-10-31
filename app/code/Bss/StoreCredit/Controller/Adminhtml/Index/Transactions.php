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

namespace Bss\StoreCredit\Controller\Adminhtml\Index;

use Bss\StoreCredit\Controller\Adminhtml\StoreCredit;
use Bss\StoreCredit\Block\Adminhtml\Transactions\Grid;

/**
 * Class Transactions
 * @package Bss\StoreCredit\Controller\Adminhtml\Index
 */
class Transactions extends StoreCredit
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Bss_StoreCredit::storecredit'
        )->_addBreadcrumb(
            __('Transactions'),
            __('Transactions')
        )->_addContent(
            $this->_view->getLayout()->createBlock(Grid::class)
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Transactions'));
        $this->_view->renderLayout();
    }
}
