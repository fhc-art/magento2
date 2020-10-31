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

namespace Bss\QuoteExtension\Model\WebApi;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request\ParamOverriderInterface;
use Magento\Quote\Api\CartManagementInterface;

/**
 * Replaces a "%cart_id%" value with the current authenticated customer's cart
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class ParamOverriderCartId implements ParamOverriderInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * Request
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * Constructs an object to override the cart ID parameter on a request.
     *
     * @param UserContextInterface $userContext
     * @param CartManagementInterface $cartManagement
     * @param RequestInterface $request
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        UserContextInterface $userContext,
        CartManagementInterface $cartManagement,
        RequestInterface $request,
        CheckoutSession $checkoutSession
    ) {
        $this->userContext = $userContext;
        $this->cartManagement = $cartManagement;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * { @inheritDoc }
     */
    public function getOverriddenValue()
    {
        try {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();
                if ($this->checkoutSession->getCheckoutIsQuoteExtension()) {
                    return $this->checkoutSession->getCheckoutIsQuoteExtension();
                }

                $referer = $this->request->getHeader('Referer');
                if (strpos($referer, 'quoteextension') !== false && $this->checkoutSession->getIsQuoteExtension()) {
                    return $this->checkoutSession->getIsQuoteExtension();
                }

                /** @var \Magento\Quote\Api\Data\CartInterface */
                $cart = $this->cartManagement->getCartForCustomer($customerId);
                if ($cart) {
                    return $cart->getId();
                }
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Current customer does not have an active cart.'));
        }
        return null;
    }
}
