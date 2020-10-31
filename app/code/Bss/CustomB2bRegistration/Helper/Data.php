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
namespace Bss\CustomB2bRegistration\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Get Approval Primary template Id
     * @return string $customerApprovePrimaryTemplate
     */
    public function getCustomerApprovePrimaryEmailTemplate($storeId = null)
    {
        $customerApprovePrimaryTemplate= $this->scopeConfig->getValue(
            'b2b/email_setting/customer_approve_primary_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $customerApprovePrimaryTemplate;
    }
}
