<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model\Config\Source\Date;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'dd/mm/yy', 'label' => __('DD/MM/YYYY')],
            ['value' => 'mm/dd/yy', 'label' => __('MM/DD/YYYY')],
            ['value' => 'yy/mm/dd', 'label' => __('YYYY/MM/DD')],
            ['value' => 'yy/dd/mm', 'label' => __('YYYY/DD/MM')],
        ];
    }
}   