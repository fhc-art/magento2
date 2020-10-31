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
namespace Bss\CustomShippingMethod\Model;

use Bss\CustomShippingMethod\Api\Data\CustomMethodInterface;

/**
 * Class CustomMethod
 *
 * @package Bss\CustomShippingMethod\Model
 */
class CustomMethod extends \Magento\Framework\Model\AbstractModel implements CustomMethodInterface
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Initialize resource model.
     */
    protected $_eventPrefix = "bss_custom_shipping_method";

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Bss\CustomShippingMethod\Model\ResourceModel\CustomMethod::class);
    }

    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return CustomMethod|\Magento\Framework\Model\AbstractModel
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get Enable.
     *
     * @return varchar
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * Set Enable
     *
     * @param  int $enable
     * @return CustomMethod|\Magento\Framework\Model\AbstractModel
     */
    public function setEnable($enable)
    {
        return $this->setData(self::ENABLE, $enable);
    }
}
