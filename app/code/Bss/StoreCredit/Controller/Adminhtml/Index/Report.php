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
use Bss\StoreCredit\Block\Adminhtml\Report\Toolbar;

/**
 * Class Report
 * @package Bss\StoreCredit\Controller\Adminhtml\Index
 */
class Report extends StoreCredit
{
    /**
     * Customer store credtit history grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Bss_StoreCredit::storecredit'
        )->_addBreadcrumb(
            __('Report'),
            __('Report')
        )->_addContent(
            $this->_view->getLayout()
                ->createBlock(Toolbar::class)
                ->setTemplate('Bss_StoreCredit::report/toolbar.phtml')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Report'));
        $this->_view->renderLayout();
        return null;
    }
}
