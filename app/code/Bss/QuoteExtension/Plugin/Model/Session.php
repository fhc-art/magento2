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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\QuoteExtension\Plugin\Model;

/**
 * Class Session
 *
 * @package Bss\QuoteExtension\Plugin\Model
 */
class Session
{
    /**
     * @var \Bss\QuoteExtension\Helper\Data
     */
    protected $quoteExtensionHelper;

    /**
     * Session constructor.
     * @param \Bss\QuoteExtension\Helper\Data $quoteExtensionHelper
     */
    public function __construct(
        \Bss\QuoteExtension\Helper\Data $quoteExtensionHelper
    ) {
        $this->quoteExtensionHelper = $quoteExtensionHelper;
    }

    /**
     * Unset the unwanted shipping rates if the quotation shipping rate is selected
     *
     * @param \Bss\QuoteExtension\Model\Session $subject
     * @param \Magento\Quote\Api\Data\CartInterface $result
     * @return \Magento\Quote\Api\Data\CartInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetQuoteExtension($subject, $result)
    {
        if (!$this->quoteExtensionHelper->validateQuantity()) {
            $result->setIsSuperMode(true);
        }

        return $result;
    }
}
