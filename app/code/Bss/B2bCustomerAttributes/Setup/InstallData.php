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
namespace Bss\B2bCustomerAttributes\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * EAV attribute
     *
     * @var eavAttribute
     */
    private $eavAttribute;

    /**
     * Init
     *
     * @param Attribute $eavAttribute
     */
    public function __construct(\Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute)
    {
        $this->eavAttribute = $eavAttribute;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $attributeIdPrefix = $this->eavAttribute->getIdByCode('customer', 'prefix');
        $attributeIdFirstname = $this->eavAttribute->getIdByCode('customer', 'firstname');
        $attributeIdMiddlename = $this->eavAttribute->getIdByCode('customer', 'middlename');
        $attributeIdLastname = $this->eavAttribute->getIdByCode('customer', 'lastname');
        $attributeIdSuffix = $this->eavAttribute->getIdByCode('customer', 'suffix');
        $attributeIdEmail = $this->eavAttribute->getIdByCode('customer', 'email');
        $attributeIdDob = $this->eavAttribute->getIdByCode('customer', 'dob');
        $attributeIdTaxvat = $this->eavAttribute->getIdByCode('customer', 'taxvat');
        $attributeIdCreatedAt = $this->eavAttribute->getIdByCode('customer', 'created_at');
        $attributeIdGender = $this->eavAttribute->getIdByCode('customer', 'gender');
        
        $data = [
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdPrefix
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdFirstname
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdMiddlename
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdLastname
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdSuffix
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdEmail
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdDob
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdTaxvat
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdCreatedAt
            ],
            [
                'form_code' => 'b2b_account_create',
                'attribute_id' => $attributeIdGender
            ],
        ];
        $setup->getConnection()->insertMultiple($setup->getTable('customer_form_attribute'), $data);
    }
}
