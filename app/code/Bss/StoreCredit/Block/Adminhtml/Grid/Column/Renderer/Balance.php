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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Backend\Block\Context;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\DataObject;

/**
 * Class Balance
 * @package Bss\StoreCredit\Block\Adminhtml\Grid\Column\Renderer
 */
class Balance extends AbstractRenderer
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @param Context $context
     * @param Data $priceHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->priceHelper = $priceHelper;
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $class = 'bss-red';
        $value = $this->priceHelper->currency($this->_getValue($row));
        if ($this->_getValue($row) > 0) {
            $class = 'bss-green';
            $value = '+'.$this->priceHelper->currency($this->_getValue($row));
        }
        return '<span class="' . $class . '"><span>' . $value . '</span></span>';
    }
}
