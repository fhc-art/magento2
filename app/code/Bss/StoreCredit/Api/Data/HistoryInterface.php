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
interface HistoryInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CUSTOMER_ID = 'customer_id';

    const CREDITMEMO_ID = 'creditmemo_id';

    const ORDER_ID = 'order_id';

    const WEBSITE_ID = 'website_id';

    const TYPE = 'type';

    const CHANGE_AMOUNT = 'change_amount';

    const BALANCE_AMOUNT = 'balance_amount';

    const COMMENT_CONTENT = 'comment_content';

    const IS_NOTIFIED = 'is_notified';

    const CREATED_TIME = 'created_time';

    const UPDATED_TIME = 'updated_time';
    /**#@-*/

    /**
     * @param int $customerId
     * @return $this
     * @since 100.1.0
     */
    public function setCustomerId($customerId);

    /**
     * @param int $creditmemoId
     * @return $this
     * @since 100.1.0
     */
    public function setCreditmemoId($creditmemoId);

    /**
     * @param int $orderId
     * @return $this
     * @since 100.1.0
     */
    public function setOrderId($orderId);

    /**
     * @param int $websiteId
     * @return $this
     * @since 100.1.0
     */
    public function setWebsiteId($websiteId);

    /**
     * @param string $type
     * @return $this
     * @since 100.1.0
     */
    public function setType($type);

    /**
     * @param float $amount
     * @return $this
     * @since 100.1.0
     */
    public function setChangeAmount($amount);

    /**
     * @param float $amount
     * @return $this
     * @since 100.1.0
     */
    public function setBalanceAmount($amount);

    /**
     * @param string $comment
     * @return $this
     * @since 100.1.0
     */
    public function setCommentContent($comment);

    /**
     * @param bool $isNotified
     * @return $this
     * @since 100.1.0
     */
    public function setIsNotified($isNotified);

    /**
     * @return int
     * @since 100.1.0
     */
    public function getCreditmemoId();

    /**
     * @return int
     * @since 100.1.0
     */
    public function getOrderId();

    /**
     * @return string
     * @since 100.1.0
     */
    public function getType();

    /**
     * @return float
     * @since 100.1.0
     */
    public function getChangeAmount();

    /**
     * @return float
     * @since 100.1.0
     */
    public function getBalanceAmount();

    /**
     * @return string
     * @since 100.1.0
     */
    public function getCommentContent();
}
