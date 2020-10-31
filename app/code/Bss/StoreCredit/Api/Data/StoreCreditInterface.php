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

namespace Bss\StoreCredit\Api\Data;

/**
 * @api
 */
interface StoreCreditInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CUSTOMER_ID = 'customer_id';

    const BALANCE_AMOUNT = 'balance_amount';

    const WEBSITE_ID = 'website_id';
    /**#@-*/

    /**
     * @param int $customerId
     * @return $this
     * @since 100.1.0
     */
    public function setCustomerId($customerId);

    /**
     * @param float $amount
     * @return $this
     * @since 100.1.0
     */
    public function setBalanceAmount($amount);

    /**
     * @param int $websiteId
     * @return $this
     * @since 100.1.0
     */
    public function setWebsiteId($websiteId);

    /**
     * @return float
     * @since 100.1.0
     */
    public function getBalanceAmount();

    /**
     * @return int
     * @since 100.1.0
     */
    public function getWebsiteId();
}
