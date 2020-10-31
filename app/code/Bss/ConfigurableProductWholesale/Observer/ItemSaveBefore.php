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
 * @category  BSS
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

/**
 * Class ItemSaveBefore
 *
 * @package Bss\ConfigurableProductWholesale\Observer
 */
class ItemSaveBefore implements ObserverInterface
{
    /**
     * Advanced tier price
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $item = $observer->getItem();
        if ($options = $item->getProduct()->getOptions()) {
            foreach ($options as $option) {
                if ($option->getType() === 'file') {
                    $optionId = $option->getOptionId();
                    $this->validateFile($item, $optionId);
                }
            }
        }
    }

    /**
     * @param $item
     * @param $optionId
     */
    private function validateFile($item, $optionId)
    {
        if (!$item->getBuyRequest()->getData('options/'.$optionId) && $item->getOptionByCode('option_'.$optionId)) {
            $optionIds = $item->getOptionByCode('option_ids');
            $arrayIds = explode(',', $optionIds->getValue());
            foreach ($arrayIds as $key => $id) {
                if ($id == $optionId) {
                    unset($arrayIds[$key]);
                }
            }
            $optionIds->setValue(implode(',', $arrayIds));
            $item->removeOption('option_'.$optionId);
        }
    }
}
