<?php
namespace Meigee\Universal\Model\Config\Source;

class CurrencySwitcher implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'currency_select', 'label' => __('Select Box'), 'img' => 'Meigee_Universal::images/currency_select.png'],
			  ['value' => 'currency_images', 'label' => __('Flags'), 'img' => 'Meigee_Universal::images/currency_images.png']
		];
    }
}