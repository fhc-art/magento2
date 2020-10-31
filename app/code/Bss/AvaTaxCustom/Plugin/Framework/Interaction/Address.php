<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * @category   BSS
 * @package    Bss_AvaTaxCustom
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AvaTaxCustom\Plugin\Framework\Interaction;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

class Address
{
    /**
     * @return array
     */
    public function afterConvertCustomerAddressToAvaTaxAddress(
        $subject,
        $result
    ) {
        if (isset($result['PostalCode']) && $result['PostalCode']) {
            $result['PostalCode'] = 90280;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function afterConvertQuoteAddressToAvaTaxAddress(
        $subject,
        $result
    ) {
        if (isset($result['PostalCode']) && $result['PostalCode']) {
            $result['PostalCode'] = 90280;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function afterConvertOrderAddressToAvaTaxAddress(
        $subject,
        $result
    ) {
        if (isset($result['PostalCode']) && $result['PostalCode']) {
            $result['PostalCode'] = 90280;
        }
        return $result;
    }
}
