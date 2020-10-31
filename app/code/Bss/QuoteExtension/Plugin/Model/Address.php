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

use Bss\QuoteExtension\Helper\QuoteExtension\Address as AddressHelper;
use Bss\QuoteExtension\Model\ManageQuote;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;

/**
 * Class Address
 *
 * @package Bss\QuoteExtension\Plugin\Model
 */
class Address
{
    /**
     * Request
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var ManageQuote
     */
    protected $manageQuote;

    /**
     * @var AddressHelper
     */
    protected $helper;

    /**
     * Address constructor.
     * @param RequestInterface $request
     * @param State $state
     * @param ManageQuote $manageQuote
     * @param AddressHelper $helper
     */
    public function __construct(
        RequestInterface $request,
        State $state,
        ManageQuote $manageQuote,
        AddressHelper $helper
    ) {
        $this->request = $request;
        $this->state = $state;
        $this->manageQuote = $manageQuote;
        $this->helper = $helper;
    }

    /**
     * Unset the unwanted shipping rates if the quotation shipping rate is selected
     *
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetGroupedAllShippingRates($subject, $result)
    {
        $quote = $subject->getQuote();
        $this->manageQuote->load($quote->getId(), 'quote_id');
        $referer = $this->request->getHeader('Referer');
        if ($quote->getQuoteExtension()
            && $subject->getShippingMethod()
            && $this->helper->isRequiredAddress()
            && 'adminhtml' != $this->state->getAreaCode()
            && $this->manageQuote->getToken() != ''
            && strpos($referer, 'quoteextension/index/index') !== false
        ) {
            foreach ($result as $key => $rate) {
                $method = $key . '_' . $rate[0]->getMethod();
                if ($subject->getShippingMethod() !== $method) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }
}
