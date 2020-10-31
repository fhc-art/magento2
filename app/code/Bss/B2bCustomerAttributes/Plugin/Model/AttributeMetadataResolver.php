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
namespace Bss\B2bCustomerAttributes\Plugin\Model;

use Magento\Customer\Model\Config\Share as ShareConfig;
use Magento\Customer\Model\FileUploaderDataResolver;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\CountryWithWebsites;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\EavValidationRules;

class AttributeMetadataResolver extends \Magento\Customer\Model\AttributeMetadataResolver
{
    /**
     * @var \Magento\Eav\Model\Config $config
     */
    protected $config;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    protected $helper;

    protected $request;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavAttribute;

    /**
     * AttributeMetadataResolver constructor.
     * @param CountryWithWebsites $countryWithWebsiteSource
     * @param EavValidationRules $eavValidationRules
     * @param FileUploaderDataResolver $fileUploaderDataResolver
     * @param ContextInterface $context
     * @param ShareConfig $shareConfig
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $helper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
     */
    public function __construct(
        CountryWithWebsites $countryWithWebsiteSource,
        EavValidationRules $eavValidationRules,
        fileUploaderDataResolver $fileUploaderDataResolver,
        ContextInterface $context,
        ShareConfig $shareConfig,
        \Magento\Eav\Model\Config $config,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Bss\CustomerAttributes\Helper\Customerattribute $helper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
    ) {
        $this->config = $config;
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->request = $request;
        $this->eavAttribute = $eavAttributeFactory;
        parent::__construct(
            $countryWithWebsiteSource,
            $eavValidationRules,
            $fileUploaderDataResolver,
            $context,
            $shareConfig
        );
    }

    /**
     * @param $subject
     * @param $result
     * @param AbstractAttribute $attribute
     * @param Type $entityType
     * @param bool $allowToShowHiddenAttributes
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetAttributesMeta(
        $subject,
        $result,
        $attribute,
        $entityType,
        $allowToShowHiddenAttributes
    ) {
        $b2bValue = "";
        $params = $this->request->getParams();
        if (isset($params['id'])) {
            $customerId = $params['id'];
            $customer = $this->customerRepository->getById($customerId);
            $b2bAttribute = $customer->getCustomAttribute('b2b_activasion_status');
            if ($b2bAttribute) {
                $b2bValue = $b2bAttribute->getValue();
            }
        }
        $fullInfo = $this->eavAttribute->create()->getAttribute('customer', $attribute->getAttributeCode());
        $usedInForms = $fullInfo->getUsedInForms();
        if(in_array('is_customer_attribute', $usedInForms)) {
            if ($b2bValue) {
                /* B2b Customer */
                if(!in_array('b2b_account_edit', $usedInForms)) {
                    $result['arguments']['data']['config']['visible'] = false;
                }
            } else {
                /* Normal Account */
                if(!in_array('customer_account_edit_frontend', $usedInForms)) {
                    $result['arguments']['data']['config']['visible'] = false;
                }
            }
        }

        return $result;
    }
}
