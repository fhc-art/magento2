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
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Plugin\Model\Metadata\Form;

/**
 * Class ValidateTextValue
 *
 * @package Bss\CustomerAttributes\Plugin\Model\Metadata\Form
 */
class ValidateTextValue
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    protected $customerAttribute;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMeta;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $area;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * ValidateValue constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $customerattribute
     * @param \Magento\Framework\App\ProductMetadataInterface $productMeta
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Bss\CustomerAttributes\Helper\Customerattribute $customerattribute,
        \Magento\Framework\App\ProductMetadataInterface $productMeta,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\State $area,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->customerAttribute = $customerattribute;
        $this->productMeta = $productMeta;
        $this->registry = $registry;
        $this->area = $area;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Fix validate for magento 2.3.1
     *
     * @param $subject
     * @param $result
     * @return bool
     */
    public function aroundValidateValue($subject, $proceed, $value)
    {
        $page = $this->request->getFullActionName();
        $errors = [];
        $attribute = $subject->getAttribute();
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('is_customer_attribute', $usedInForms)) {
            if (!$this->customerAttribute->getConfig('bss_customer_attribute/general/enable')) {
                return true;
            }
            $enableCustomerAttribute = $this->scopeConfig->getValue('bss_customer_attribute/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($value === false) {
                // try to load original value and validate it
                $value = $subject->getEntity()->getDataUsingMethod($attribute->getAttributeCode());
            }
            $frontendInput = $attribute->getFrontendInput();
            if ($attribute->getIsRequired() &&
                empty($value) && $value !== '0' &&
                $attribute->getDefaultValue() === null
            ) {
                if ($this->area->getAreaCode() == "adminhtml") {
                    if (!in_array('b2b_account_create', $usedInForms) || !in_array('customer_account_create_frontend', $usedInForms)) {
                        return true;
                    }
                }
                if((!in_array('b2b_account_create', $usedInForms) || !$enableCustomerAttribute) && $page == 'btwob_account_createpost') {
                    return true;
                }
                if((!in_array('b2b_account_edit', $usedInForms) || !$enableCustomerAttribute || !in_array('customer_account_edit_frontend', $usedInForms)) && $page == 'customer_account_editPost') {
                    return true;
                }

                if (!in_array('customer_account_create_frontend', $usedInForms) && $page == 'customer_account_createpost') {
                    return true;
                }

                $label = __($attribute->getStoreLabel());
                $errors[] = __('"%1" is a required value.', $label);
            }
        }
        
        if (!empty($errors)) {
            return $errors;
        }

        return $proceed($value);
    }
}
