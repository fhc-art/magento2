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
 * @category  BSS
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Model\Config\Source;

/**
 * Class Customer
 *
 * @package Bss\ConfigurableProductWholesale\Model\Config\Source
 */
class Customer implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    private $collectionCustomer;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $collectionCustomer
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $collectionCustomer
    ) {
    
        $this->collectionCustomer = $collectionCustomer;
    }

    /**
     * Return array of customer group config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        foreach ($this->collectionCustomer->getData() as $key => $group) {
            $data[$key]['value'] = $group['customer_group_id'];
            $data[$key]['label'] = $group['customer_group_code'];
        }
        return $data;
    }
}
