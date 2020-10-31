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

namespace Bss\StoreCredit\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Class AddCheckboxToCreditmemoTotals
 * @package Bss\StoreCredit\Observer
 */
class AddCheckboxToCreditmemoTotals implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        if (($action == 'updateQty' || $action == 'new') && $moduleName == 'sales' &&
            $controller == 'order_creditmemo' && $observer->getElementName() == 'submit_before') {
            $html = '<div class="field choice admin__field admin__field-option field-storecredit-checkbox">
                        <input id="storecredit-refund"
                               class="admin__control-checkbox"
                               name="creditmemo[storecredit]"
                               value="1"
                               type="checkbox" />
                        <label for="storecredit-refund" class="admin__field-label">
                            <span>';
            $html .= __('Refund all to Store Credit');
            $html .='</span>
                        </label>
                    </div>';
            $output = $observer->getTransport()->getOutput() . $html;
            $observer->getTransport()->setOutput($output);
        }
    }
}
