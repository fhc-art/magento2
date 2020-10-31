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
namespace Bss\CustomB2bRegistration\Plugin\Model\Config\Source;

class CustomerAttribute
{
    const B2B_APPROVAL_PRIMARY = 4;

    /**
     * @param \Bss\B2bRegistration\Model\Config\Source\CustomerAttribute $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllOptions($subject, $result)
    {
        $options = $result;
        $options[] = [
            'label' => __('B2B Approval, Primary Account'),
            'value' => self::B2B_APPROVAL_PRIMARY,
        ];

        return $options;
    }
}
