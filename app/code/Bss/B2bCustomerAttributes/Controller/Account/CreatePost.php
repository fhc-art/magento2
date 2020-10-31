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
 * @package    Bss_B2bCustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Bss\B2bRegistration\Model\Config\Source\CustomerAttribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;

class CreatePost extends \Bss\B2bRegistration\Controller\Account\CreatePost
{
    /**
     * @return string
     */
    protected function getFormExtract()
    {
        return 'b2b_account_create';
    }
}