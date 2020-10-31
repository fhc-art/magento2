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
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Plugin\Model\ResourceModel;

class CustomerRepository
{
    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    /**
     * CustomerRepository constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null $passwordHash
     * @return mixed
     */
    public function aroundSave(
        $subject,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $passwordHash = null
    ) {
        if ($this->registry->registry('bss_customer')) {
            $this->registry->unregister('bss_customer');
            $this->registry->register('bss_customer', $customer);
        } else {
            $this->registry->register('bss_customer', $customer);
        }

        return $proceed($customer, $passwordHash);
    }
}
