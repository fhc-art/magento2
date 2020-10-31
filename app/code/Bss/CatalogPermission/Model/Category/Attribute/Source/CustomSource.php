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
 * @package    Bss_CatalogPermission
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CatalogPermission\Model\Category\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class CustomSource
 *
 * @package Bss\CatalogPermission\Model\Category\Attribute\Source
 */
class CustomSource extends AbstractSource
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroup;

    /**
     * Custom constructor.
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     */
    public function __construct(
        Collection $customerGroup
    ) {
        $this->customerGroup = $customerGroup;
    }

    /**
     * Get all customer group
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $customerGroups = $this->customerGroup->toOptionArray();
            $this->_options = $customerGroups;
        }
        return $this->_options;
    }
}
