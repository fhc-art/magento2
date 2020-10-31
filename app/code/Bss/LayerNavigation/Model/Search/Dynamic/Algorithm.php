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
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Model\Search\Dynamic;

/**
 * Class Algorithm
 * @package Bss\LayerNavigation\Model\ResourceModel\Search\Dynamic
 */
class Algorithm extends \Magento\Framework\Search\Dynamic\Algorithm
{
    /**
     * Flush _lastValueLimiter
     *
     * @param \Magento\Framework\Search\Dynamic\IntervalInterface $interval
     * @return array
     */
    public function calculateSeparators(\Magento\Framework\Search\Dynamic\IntervalInterface $interval)
    {
        $this->_lastValueLimiter = [null, 0];
        return parent::calculateSeparators($interval);
    }
}
