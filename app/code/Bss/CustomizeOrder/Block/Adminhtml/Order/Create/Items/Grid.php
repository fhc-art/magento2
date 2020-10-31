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
 * @category   BSS
 * @package    Bss_CustomizeOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomizeOrder\Block\Adminhtml\Order\Create\Items;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
{
    /**
     * Set path to template used for generating block's output.
     *
     * @param string $template
     * @return \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
     */
    public function setTemplate($template)
    {
        $this->_template = 'Bss_CustomizeOrder::order/create/items/grid.phtml';
        return $this;
    }
}
