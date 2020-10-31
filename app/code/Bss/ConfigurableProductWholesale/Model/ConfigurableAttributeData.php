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

namespace Bss\ConfigurableProductWholesale\Model;

use Magento\Catalog\Model\Product;

class ConfigurableAttributeData extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeData
{
    /**
     * @param Product $product
     * @param array $options
     * @param bool|null $tableOrdering
     * @return array
     */
    public function getAttributesDataTableOrdering(
        Product $product,
        array $options = [],
        $tableOrdering = null
    ) {
        $defaultValues = [];
        $attributes = [];
        $attributesConfig = $product->getTypeInstance()->getConfigurableAttributes($product);
        foreach ($attributesConfig as $attribute) {
            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);
            if ($attributeOptionsData) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();
                $attributes[$attributeId] = [
                    'id' => $attributeId,
                    'code' => $productAttribute->getAttributeCode(),
                    'label' => $productAttribute->getStoreLabel($product->getStoreId()),
                    'options' => $attributeOptionsData,
                    'position' => $attribute->getPosition(),
                ];
                $defaultValues[$attributeId] = $this->getAttributeConfigValue($attributeId, $product);
            }
        }

        $table = array_pop($attributes);
        if (isset($tableOrdering) && isset($table)) {
            return $table;
        }
        return [
            'attributes' => $attributes,
            'defaultValues' => $defaultValues,
        ];
    }

    /**
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getTableOrdering(Product $product, array $options = [])
    {
        $tableOrdering = true;
        $table = $this->getAttributesDataTableOrdering($product, $options, $tableOrdering);
        return $table;
    }
}
