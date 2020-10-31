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
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomizeCmindMultiAccount\Plugin\Helper;

use Magento\Customer\Model\Session as CustomerSession;

class View
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * View constructor.
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Cminds\MultiUserAccounts\Helper\View $subject
     * @param callable $proceed
     * @param null $customerId
     * @return bool
     */
    public function aroundCanManageSubaccounts(
        \Cminds\MultiUserAccounts\Helper\View $subject,
        callable $proceed,
        $customerId = null
    ) {
        if (!$customerId && !$this->customerSession->getCustomerId()) {
            return false;
        }
        return $proceed($customerId);
    }
}
