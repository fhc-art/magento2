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
 * @package    Bss_CustomShippingMethod
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomShippingMethod\Api\Data;

interface CustomMethodInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = 'id';
    const ENABLE = 'enabled';

    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set EntityId.
     *
     * @param int $entityId
     * @return int
     */
    public function setEntityId($entityId);

    /**
     * Get Enable.
     *
     * @return int
     */
    public function getEnable();

    /**
     * Set Enable.
     *
     * @param int $enable
     * @return int
     */
    public function setEnable($enable);
}
