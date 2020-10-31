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
 * @package    Bss_CanonicalTag
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CanonicalTag\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class SelectCategoryPath
 *
 * @package Bss\CanonicalTag\Model
 */
class SelectCategoryPath extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(\Bss\CanonicalTag\Model\ResourceModel\SelectCategoryPath::class);
    }
}
