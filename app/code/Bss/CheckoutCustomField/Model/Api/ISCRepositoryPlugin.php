<?php
/**
 *  BSS Commerce Co.
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category    BSS
 * @package     Bss_CheckoutCustomField
 * @author      Extension Team
 * @copyright   Copyright © 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license     http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CheckoutCustomField\Model\Api;

use Bss\CheckoutCustomField\Model\Observer\Sales\CustomFieldHelper;

/**
 * Class ISCRepositoryPlugin
 *
 * @package Bss\CheckoutCustomField\Model\Api
 */
class ISCRepositoryPlugin
{
    /**
     * @var CustomFieldHelper
     */
    private $customFieldHelper;

    /**
     * ISCRepositoryPlugin constructor.
     *
     * @param CustomFieldHelper $customFieldHelper
     */
    public function __construct(
        CustomFieldHelper $customFieldHelper
    ) {
        $this->customFieldHelper = $customFieldHelper;
    }

    /**
     * Set custom field to invoice
     *
     * @param object $subject
     * @param object $result
     * @return object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        $subject,
        $result
    ) {
        /** @var  $order */
        foreach ($result->getItems() as $item) {
            $this->customFieldHelper->executeSetExtensionAttributes(null, $item);
        }
        return $result;
    }
}
