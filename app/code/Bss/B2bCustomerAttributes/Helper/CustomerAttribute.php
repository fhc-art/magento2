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
 * @copyright  Copyright (c) 2015-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Helper;

use Magento\Framework\App\Helper\Context;

class CustomerAttribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavAttribute;

    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    protected $customerattribute;

    /**
     * CustomerAttribute constructor.
     * @param Context $context
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $customerattribute
     * @param \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
     */
    public function __construct(
        Context $context,
        \Bss\CustomerAttributes\Helper\Customerattribute $customerattribute,
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
    ) {
        $this->customerattribute = $customerattribute;
        $this->eavAttribute = $eavAttributeFactory;
        parent::__construct($context);
    }

    /**
     * @param $statusCustomer
     * @param $attributeCode
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkAttrForEditPage($statusCustomer, $attributeCode)
    {
        if (!$statusCustomer || $statusCustomer == '0') {
            return $this->isAttributeForNormalAccountEdit($attributeCode);
        } elseif (isset($statusCustomer) && $statusCustomer != '0') {
            return $this->isAttributeForB2bAccountEdit($attributeCode);
        }
        return false;
    }

    /**
     * @param $attributeCode
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAttributeForNormalAccountEdit($attributeCode)
    {
        $attribute = $this->eavAttribute->create()
            ->getAttribute('customer', $attributeCode);
        $usedInForms = $attribute->getUsedInForms();

        if (in_array('customer_account_edit_frontend', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @param $attributeCode
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAttributeForB2bAccountEdit($attributeCode)
    {
        $attribute = $this->eavAttribute->create()
            ->getAttribute('customer', $attributeCode);
        $usedInForms = $attribute->getUsedInForms();

        if (in_array('b2b_account_edit', $usedInForms)) {
            return true;
        }
        return false;
    }
}
