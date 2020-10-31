<?php
namespace Meigee\Universal\Model\Config\Source;

class SiteSkins implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'default.css', 'label' => __('Default'), 'img' => 'Meigee_Universal::images/default.jpg', 'header' => 'default'],
            ['value' => 'wine_2.css', 'label' => __('Wine 2'), 'img' => 'Meigee_Universal::images/wine_2.jpg', 'header' => 'wine_2'],
            ['value' => 'electronics.css', 'label' => __('Electronics'), 'img' => 'Meigee_Universal::images/electronics.jpg', 'header' => 'electronics'],
            ['value' => 'food_2.css', 'label' => __('Food 2'), 'img' => 'Meigee_Universal::images/food_2.jpg', 'header' => 'food_2'],
            ['value' => 'design.css', 'label' => __('Design'), 'img' => 'Meigee_Universal::images/design.jpg', 'header' => 'design'],
            ['value' => 'grocery.css', 'label' => __('Grocery'), 'img' => 'Meigee_Universal::images/grocery.jpg', 'header' => 'grocery'],
            ['value' => 'food.css', 'label' => __('Food'), 'img' => 'Meigee_Universal::images/food.jpg', 'header' => 'food'],
            ['value' => 'cars.css', 'label' => __('Cars'), 'img' => 'Meigee_Universal::images/cars.jpg', 'header' => 'cars'],
            ['value' => 'hardware.css', 'label' => __('Hardware & Tools'), 'img' => 'Meigee_Universal::images/hardware.jpg', 'header' => 'hardware'],
            ['value' => 'lingerie.css', 'label' => __('Lingerie'), 'img' => 'Meigee_Universal::images/lingerie.jpg', 'header' => 'lingerie'],
            ['value' => 'sport.css', 'label' => __('Sport'), 'img' => 'Meigee_Universal::images/sport.jpg', 'header' => 'sport'],
            ['value' => 'food_3.css', 'label' => __('Food 3'), 'img' => 'Meigee_Universal::images/food_3.jpg', 'header' => 'food_3'],
            ['value' => 'furniture.css', 'label' => __('Furniture'), 'img' => 'Meigee_Universal::images/furniture.jpg', 'header' => 'furniture'],
            ['value' => 'perfume.css', 'label' => __('Perfume'), 'img' => 'Meigee_Universal::images/perfume.jpg', 'header' => 'perfume'],
        ];
  }
}