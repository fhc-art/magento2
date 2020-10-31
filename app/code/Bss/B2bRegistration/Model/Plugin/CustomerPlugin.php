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
namespace Bss\B2bRegistration\Model\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomerPlugin
 * @package Bss\B2bRegistration\Model\Plugin
 */
class CustomerPlugin extends \Magento\Newsletter\Model\Plugin\CustomerPlugin
{
    /**
     * Plugin after create customer that updates any newsletter subscription that may have existed.
     *
     * If we have extension attribute (is_subscribed) we need to subscribe that customer
     *
     * @param CustomerRepository $subject
     * @param CustomerInterface $result
     * @param CustomerInterface $customer
     * @return CustomerInterface
     */
    public function afterSave(
        CustomerRepository $subject,
        CustomerInterface $result,
        CustomerInterface $customer = null
    ) {
        $customerSession = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Customer\Model\Session::class
        );
        if ($customerSession->getBssSaveAccount()) {
            return $result;
        } else {
            return parent::afterSave($subject, $result, $customer);
        }
    }
}
