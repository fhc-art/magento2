<?php
 
namespace DCKAP\CustomizedOrderSearch\Model\Config\Source;
 
class Options extends \DCKAP\Ordersearch\Model\Config\Source\Options
{

    const PO_NUM = 'purchase_order';

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
            ['value' => self::PO_NUM, 'label' => __('Customer Purchase Order Number')],
        ];
    }

    public function toArray()
    {
        return [
            self::INCREMENT_ID => __('Order Id'),
            self::NAME => __('Product Name'),
            self::SKU => __('SKU'),
            self::POSTCODE => __('Zip Code'),
            self::CITY => __('City'),
            self::PO_NUM => __('Customer Purchase Order Number')
        ];

    }

}
?>