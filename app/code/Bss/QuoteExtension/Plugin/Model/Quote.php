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

use Bss\QuoteExtension\Model\ManageQuote;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\RequestInterface;

/**
 * Class Quote
 *
 * @package Bss\QuoteExtension\Plugin\Model
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Quote
{
    /**
     * @var ManageQuote
     */
    private $manageQuote;

    /**
     * Request
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Constructs an object to override the cart ID parameter on a request.
     *
     * @param ManageQuote $manageQuote
     * @param RequestInterface $request
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        ManageQuote $manageQuote,
        RequestInterface $request,
        CheckoutSession $checkoutSession
    ) {
        $this->manageQuote = $manageQuote;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Unset the unwanted shipping rates if the quotation shipping rate is selected
     *
     * @param \Bss\QuoteExtension\Model\Quote $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetIsActive($subject, $result)
    {
        $referer = $this->request->getHeader('Referer');
        $this->manageQuote->load($subject->getId(), 'quote_id');
        if ($subject->getQuoteExtension()
            && $this->manageQuote->getToken()
            && strpos($referer, 'quoteextension/index/index') !== false
        ) {
            return true;
        }

        return $result;
    }

    /**
     * @param $subject
     * @param $result
     * @param $quoteId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoadByIdWithoutStore($subject, $result, $quoteId)
    {
        if ($result->getQuoteExtension() && !$result->getData('is_active')) {
            $result->setIsSuperMode(true);
        }
        return $result;
    }
}
