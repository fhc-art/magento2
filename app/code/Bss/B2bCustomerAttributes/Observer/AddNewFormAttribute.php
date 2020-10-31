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
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddNewFormAttribute implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * AddNewFormAttribute constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $usedInForms = $observer->getEvent()->getData('usedInForms');
        $data = $observer->getEvent()->getData('dataPost');
        $num = count($usedInForms->getData()) + 1;
        if (isset($data['b2b_account_create']) && $data['b2b_account_create'] == 1) {
            $usedInForms[$num] = 'b2b_account_create';
        }
        if (isset($data['b2b_account_edit']) && $data['b2b_account_edit'] == 1) {
            $usedInForms[$num + 1] = 'b2b_account_edit';
        }
    }
}
