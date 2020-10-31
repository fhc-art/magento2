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
namespace Bss\B2bCustomerAttributes\Model\Customer;

use Magento\Eav\Model\Entity\Type;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @api
 * @since 100.0.2
 */
class DataProvider extends \Magento\Customer\Model\Customer\DataProvider
{

    /**
     * Get attributes meta
     *
     * @param Type $entityType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAttributesMeta(Type $entityType)
    {
        $meta = parent::getAttributesMeta($entityType);
        $request = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Request\Http');
        $params = $request->getParams();
        $attributes = $entityType->getAttributeCollection();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($attributeCode == "b2b_activasion_status") {
                continue;
            }
            $usedInForms = $attribute->getUsedInForms();

            if(in_array('is_customer_attribute', $usedInForms)) {
                $customerId = $params['id'];
                if (isset($this->getData()[$customerId]['customer']['b2b_activasion_status']) && $this->getData()[$customerId]['customer']['b2b_activasion_status']) {
                    /* B2b Customer */
                    if(!in_array('b2b_account_edit', $usedInForms)) {
                        unset($meta[$attributeCode]);
                    }
                } else {
                    /* Normal Account */
                    if(!in_array('customer_account_edit_frontend', $usedInForms)) {
                        unset($meta[$attributeCode]);
                    }
                }
            }
        }

        return $meta;
    }
}
