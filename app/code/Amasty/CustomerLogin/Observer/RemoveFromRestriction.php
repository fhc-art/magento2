<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Observer;

use Magento\Framework\Event\Observer;

class RemoveFromRestriction implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getData('controller');
        $result = $observer->getData('result');

        if (get_parent_class($controller) === \Amasty\CustomerLogin\Controller\Index\Index::class
            || get_class($controller) === \Amasty\CustomerLogin\Controller\Index\Index::class) {
            $result->setShouldProceed(false);
        }
    }
}
