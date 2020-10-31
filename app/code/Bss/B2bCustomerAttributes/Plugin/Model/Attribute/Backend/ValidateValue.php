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
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Plugin\Model\Attribute\Backend;

class ValidateValue
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $area;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavAttribute;

    /**
     * ValidateValue constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $area
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $area,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->area = $area;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
        $this->eavAttribute = $eavAttributeFactory;
    }

    public function aroundValidate(
        $subject,
        callable $proceed,
        $object
    ) {
    	$attribute = $this->eavAttribute->create()
            ->getAttribute('customer', $subject->getAttribute()->getAttributeCode());
        if (isset($attribute)) {
            $usedInForms = $attribute->getUsedInForms();
            $attrCode = $attribute->getAttributeCode();

            if(in_array('is_customer_attribute', $usedInForms) && $attribute->getIsRequired()) {
                if (!in_array('b2b_account_edit', $usedInForms) || !in_array('customer_account_edit_frontend', $usedInForms)) {
                    return true;
                }
                
            }
        }
        
        return $proceed($object);
    }
}
