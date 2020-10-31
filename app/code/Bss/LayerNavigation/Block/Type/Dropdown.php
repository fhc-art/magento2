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
namespace Bss\LayerNavigation\Block\Type;

class Dropdown extends AbstractType
{
    /**
     * @var string
     */
    protected $_template = 'Bss_LayerNavigation::type/dropdown.phtml';

    /**
     * @return array
     */
    public function getOptions()
    {
        $filterModel = $this->getFilterModel();
        $options = [
            [
                'value' => '',
                'label' => '',
                'selected' => false,
                'disabled' => false
            ]
        ];

        $isShowCounter = $filterModel->isShowCounter();

        foreach ($this->getItems() as $filterItem) {
            $isShowZero = $filterModel->isShowZero($filterItem->getFilter()->getAttributeModel());
            if ($filterItem->getCount() == 0 && !$isShowZero) {
                continue;
            }

            $label = $filterItem->getLabel();
            if ($isShowCounter) {
                $label .= ' (' . $filterItem->getCount() . ')';
            }

            $options[] = [
                'value' => $filterModel->getItemUrl($filterItem),
                'label' => $label,
                'selected' => $filterModel->isSelected($filterItem),
                'disabled' => ($filterItem->getCount() == 0) ? true : false
            ];
        }

        return $options;
    }
}
