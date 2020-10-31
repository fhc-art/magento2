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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_OrderAmount
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OrderAmount\Model\Config\Backend;

/**
 * Class Serialized
 *
 * @package Bss\OrderAmount\Model\Config\Backend
 */
class Serialized extends \Magento\Framework\App\Config\Value
{
    /**
     * @return \Magento\Framework\App\Config\Value|void
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : unserialize($value));
        }
    }

    /**
     * @return \Magento\Framework\App\Config\Value
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function beforeSave()
    {
        $values = $this->getValue();
        if (is_array($values)) {
            unset($values['__empty']);
            foreach ($values as $value) {
                if (is_array($value)) {
                    $miniumAmount = $value['minimum_amount'];
                    if (!is_numeric($miniumAmount)) {
                        throw new \Magento\Framework\Exception\ValidatorException(__(
                            'Minimum Amount is not a number.'
                        ));
                    } elseif ($miniumAmount < 0) {
                        throw new \Magento\Framework\Exception\ValidatorException(__(
                            'Minimum Amount must be greater than zero'
                        ));
                    }
                }
            }
        }
        $this->setValue($values);

        if (is_array($this->getValue())) {
            $this->setValue(serialize($this->getValue()));
        }
        return parent::beforeSave();
    }
}
