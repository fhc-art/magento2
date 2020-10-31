<?php
/**
 *  BSS Commerce Co.
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category    BSS
 * @package     Bss_CheckoutCustomField
 * @author      Extension Team
 * @copyright   Copyright © 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license     http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CheckoutCustomField\Model\Observer\Sales;

use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class CustomFieldHelper
 *
 * @package Bss\CheckoutCustomField\Model\Observer\Sales
 */
class CustomFieldHelper
{
    protected $extensionObject;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var \Bss\CheckoutCustomField\Helper\Data
     */
    private $helper;

    /**
     * CustomFieldHelper constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param \Bss\CheckoutCustomField\Helper\Data $helper
     */
    public function __construct(
        JsonHelper $jsonHelper,
        \Bss\CheckoutCustomField\Helper\Data $helper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
    }

    /**
     * Set extension attribute to object
     *
     * @param object $order
     * @param object $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function executeSetExtensionAttributes($order = null, $object = null)
    {
        $customField = [];
        if ($order == null) {
            $order = $object->getOrder();
        } else {
            $object = $order;
        }
        $extensionAttributes = $this->getExtensionAttributes($object);
        if ($extensionAttributes
            && ($customFieldEncoded = $order->getData('bss_customfield'))
            && $this->helper->moduleEnabled()
        ) {
            $customFields = $this->jsonHelper->jsonDecode($customFieldEncoded);
            foreach ($customFields as $field) {
                if ($field['show_in_order'] == '1') {
                    if (is_array($field['value']) && !empty($field['value'])) {
                        $str = "";
                        foreach ($field['value'] as $value) {
                            $str .= $value . ", ";
                        }
                        $customField[] = $field['frontend_label'] . " : " . $str;
                    } elseif ($field['value'] != "") {
                        $customField[] = $field['frontend_label'] . " : " . $field['value'];
                    }
                }
            }
            $extensionAttributes->setBssCustomfield($customField);
        }
        $object->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Set extension object
     *
     * @param object $extensionObject
     *
     * @return void
     */
    public function setExtensionObject($extensionObject)
    {
        $this->extensionObject = $extensionObject;
    }

    /**
     * Get extension attributes from subject param
     *
     * @param object $subject
     * @return object
     */
    protected function getExtensionAttributes($subject)
    {
        $extensionAttributes = $subject->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionObject;
        }
        return $extensionAttributes;
    }
}
