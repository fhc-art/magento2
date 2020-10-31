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
namespace Bss\QuoteExtension\Block\QuoteExtension\Quote\Item;

/**
 * Class Renderer
 *
 * @package Bss\QuoteExtension\Block\QuoteExtension\Quote\Item
 */
class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * @var \Bss\QuoteExtension\Helper\Data
     */
    protected $quoteExtensionHelper;

    /**
     * @var \Bss\QuoteExtension\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Renderer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $messageInterpretationStrategy
     * @param \Bss\QuoteExtension\Helper\Data $quoteExtensionHelper
     * @param \Bss\QuoteExtension\Model\QuoteFactory $quoteFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $messageInterpretationStrategy,
        \Bss\QuoteExtension\Helper\Data $quoteExtensionHelper,
        \Bss\QuoteExtension\Model\QuoteFactory $quoteFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
        $this->quoteExtensionHelper = $quoteExtensionHelper;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Check quote can accept
     *
     * @return boolean
     */
    public function canAccept()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        if (!$quoteId) {
            return false;
        }
        $quote = $this->quoteFactory->create()->load($quoteId);

        return $this->quoteExtensionHelper->canAccept($quote);
    }

    /**
     * Get delete item url
     *
     * @param int $tierItemId
     * @return string
     */
    public function getDeleteUrl($tierItemId)
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        if (!$quoteId) {
            return '';
        }

        return $this->getUrl('quoteextension/quote/delete', ['id' => $tierItemId, 'quote_id' => $quoteId]);
    }

    /**
     * @return \Bss\QuoteExtension\Helper\QuoteExtensionCart
     */
    public function getQuoteExtensionCartHelper()
    {
        return $this->quoteExtensionCartHelper;
    }
}
