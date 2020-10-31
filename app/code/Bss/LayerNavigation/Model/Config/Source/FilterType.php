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
namespace Bss\LayerNavigation\Model\Config\Source;

use Bss\LayerNavigation\Helper\Data as LayerHelper;

class FilterType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var LayerHelper
     */
    protected $helper;

    /**
     * FilterType constructor.
     * @param LayerHelper $helper
     */
    public function __construct(LayerHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $displayTypes = $this->helper->getDisplayTypes();

        $options = [];
        foreach ($displayTypes as $key => $type) {
            if (isset($type['label'])) {
                $options[$key] = $type['label'];
            }
        }
        return $options;
    }
}
