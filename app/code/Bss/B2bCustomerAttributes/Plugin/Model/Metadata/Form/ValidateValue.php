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
namespace Bss\B2bCustomerAttributes\Plugin\Model\Metadata\Form;

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
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMeta;

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
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMeta
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->area = $area;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
        $this->eavAttribute = $eavAttributeFactory;
        $this->productMeta = $productMeta;
    }

    /**
     * @param $subject
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterValidateValue(
        $subject,
        $result
    ) {
        $page = $this->request->getFullActionName();
    	$attribute = $this->eavAttribute->create()
            ->getAttribute('customer', $subject->getAttribute()->getAttributeCode());
        if (isset($attribute)) {
        	$usedInForms = $attribute->getUsedInForms();
            $enableCustomerAttribute = $this->scopeConfig->getValue('bss_customer_attribute/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if(in_array('is_customer_attribute', $usedInForms) && $attribute->getIsRequired()) {
                $newB2bValue = "";
                /* Backend Validate */
                if ($this->area->getAreaCode() == "adminhtml") {
                    if (!in_array('b2b_account_edit', $usedInForms) || !in_array('customer_account_edit_frontend', $usedInForms)) {
                        return true;
                    }
                }

            
                if((!in_array('b2b_account_create', $usedInForms) || !$enableCustomerAttribute) && $page == 'btwob_account_createpost') {
                    return true;
                }
                if((!in_array('b2b_account_edit', $usedInForms) || !$enableCustomerAttribute) && $page == 'customer_account_editPost') {
                    return true;
                }
                if (!in_array('b2b_account_create', $usedInForms) || !in_array('customer_account_create_frontend', $usedInForms)) {
                    return true;
                }
            }
        }

        return $result;
    }
}
