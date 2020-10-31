<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model\Config\Source;

class Options implements \Magento\Framework\Option\ArrayInterface
{

    const INCREMENT_ID = 'increment_id';

    const NAME = 'name';

    const SKU = 'sku';

    const POSTCODE = 'postcode';

    const CITY = 'city';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::INCREMENT_ID, 'label' => __('Order Id')],
            ['value' => self::NAME, 'label' => __('Product Name')],
            ['value' => self::SKU, 'label' => __('SKU')],
            ['value' => self::POSTCODE, 'label' => __('Zip Code')],
            ['value' => self::CITY, 'label' => __('City')],
        ];
    }

    public function toArray()
    {
        return [
            self::INCREMENT_ID => __('Order Id'),
            self::NAME => __('Product Name'),
            self::SKU => __('SKU'),
            self::POSTCODE => __('Zip Code'),
            self::CITY => __('City')
        ];

    }


}   