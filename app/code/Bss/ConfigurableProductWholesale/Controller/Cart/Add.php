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
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Controller\Cart;

use Magento\Checkout\Controller\Cart\Add as CheckoutAdd;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Bss\ConfigurableProductWholesale\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Escaper;
use Magento\Checkout\Helper\Cart;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filter\LocalizedToNormalized;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class Add
 *
 * @package Bss\ConfigurableProductWholesale\Controller\Cart
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends CheckoutAdd
{
    /**
     * @var Data
     */
    private $helperBss;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var Cart
     */
    private $cartHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LocalizedToNormalized
     */
    private $localFilter;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @return array
     */
    protected $options = [];

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Add constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helperBss
     * @param ProductFactory $productFactory
     * @param Escaper $escaper
     * @param Cart $cartHelper
     * @param LoggerInterface $logger
     * @param LocalizedToNormalized $localFilter
     * @param ResolverInterface $localeResolver
     * @param DecoderInterface $jsonDecoder
     * @param StockRegistryInterface $stockRegistry
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        Data $helperBss,
        ProductFactory $productFactory,
        Escaper $escaper,
        Cart $cartHelper,
        LoggerInterface $logger,
        LocalizedToNormalized $localFilter,
        ResolverInterface $localeResolver,
        DecoderInterface $jsonDecoder,
        StockRegistryInterface $stockRegistry
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
        $this->storeManager = $storeManager;
        $this->helperBss = $helperBss;
        $this->productFactory = $productFactory;
        $this->escaper = $escaper;
        $this->cartHelper = $cartHelper;
        $this->logger = $logger;
        $this->localFilter = $localFilter;
        $this->localeResolver = $localeResolver;
        $this->jsonDecoder = $jsonDecoder;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Add product to shopping cart action
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
        $stockItem = $this->stockRegistry->getStockItem($params['product'])->getData();
        if (!$this->helperBss->getConfig() || !isset($params['bss-table-ordering'])) {
            return parent::execute();
        }
        try {
            $productDefault = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
            /**
             * Check product availability
             */
            if (!$productDefault) {
                return $this->goBack();
            }
            $product = $this->_initProduct();
            $success = $this->validateAjax($params, $product, $stockItem['is_qty_decimal']);
            if ($success) {
                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', $related));
                }

                $this->cart->save();
                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    [
                        'product' => $product,
                        'request' => $this->getRequest(),
                        'response' => $this->getResponse()
                    ]
                );

                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                        $message = __(
                            'You added %1 to your shopping cart.',
                            $product->getName()
                        );
                        $this->messageManager->addSuccessMessage($message);
                    }
                    return $this->goBack(null, $product);
                }
            } else {
                $this->messageManager->addErrorMessage(
                    __('No items add to your shopping cart.')
                );
                return $this->goBack();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $url = $this->catchException($e);
            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add this item to your shopping cart right now.')
            );
            $this->logger->critical($e);
            return $this->goBack();
        }
    }

    /**
     * @param null $productId
     * @return bool|\Magento\Catalog\Model\Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProduct($productId = null)
    {
        if ($productId) {
            $storeId = $this->storeManager->getStore()->getId();
            $product = $this->productFactory->create()->setStoreId($storeId)->load($productId);
            return $product;
        }
        return false;
    }

    /**
     * Add all product to cart with ajax product
     *
     * @param $params
     * @param $qtyDecimal
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function addMultipleProductAjax($params, $qtyDecimal)
    {
        $success = false;

        if (isset($params['bss-addtocart-data']) && $params['bss-addtocart-data']) {
            $optionsEncode = urldecode($params['bss-addtocart-data']);
            $paramData = $this->jsonDecoder->decode($optionsEncode);
            foreach ($paramData as $data) {
                $qty = $data['qty'];

                if ($qtyDecimal == '0') {
                    $qty = floor($qty);
                }
                if ($qty <= 0 || !isset($data['data'])) {
                    continue;
                }
                $paramsTableOrdering = [];
                $paramsTableOrdering['product'] = $params['product'];
                $product = $this->getProduct($params['product']);
                if (isset($qty)) {
                    $this->localFilter->setOptions(['locale' => $this->localeResolver->getLocale()]);
                    $qty = $this->localFilter->filter((double)$qty);
                }
                $paramsTableOrdering['qty'] = $qty;
                $superAttribute = [];
                foreach ($data['data'] as $key => $optionValue) {
                    $optionId = str_replace('data-option-', '', $key);
                    $superAttribute[$optionId] = $optionValue;
                }
                $paramsTableOrdering['super_attribute'] = $superAttribute;
                $paramsTableOrdering['options'] = isset($params['options']) ? $params['options'] : [];
                if (!empty($this->options)) {
                    $paramsTableOrdering['options'] = $this->options;
                } else {
                    $paramsTableOrdering['options'] = $paramsTableOrdering['options'];
                }
                $paramsTableOrdering['selected_configurable_option'] = $params['selected_configurable_option'];
                $this->getRequest()->setParam('qty', $qty);
                $this->cart->addProduct($product, $paramsTableOrdering);
                $this->getCustomOption($product);
                $success = true;
            }
        }
        return $success;
    }

    /**
     * @param $product
     */
    protected function getCustomOption($product)
    {
        if (empty($this->options)) {
            $cartItem = $this->cart->getQuote()->getItemByProduct($product);
            $this->options = $cartItem->getBuyRequest()->getOptions();
        }
    }

    /**
     * Add all product to cart
     *
     * @param $params
     * @param $qtyDecimal
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addMultipleProduct($params, $qtyDecimal)
    {
        $success = false;
        if (!empty($params['bss-qty'])) {
            foreach ($params['bss-qty'] as $row => $qty) {
                if ($qty <= 0) {
                    continue;
                }
                if ($qtyDecimal == '0') {
                    $qty = floor($qty);
                }
                $paramsTableOrdering = [];

                $product = $this->getProduct($params['product']);
                if (isset($qty)) {
                    $this->localFilter->setOptions(['locale' => $this->localeResolver->getLocale()]);
                    $qty = $this->localFilter->filter((double)$qty);
                }
                $paramsTableOrdering['qty'] = $qty;
                $paramsTableOrdering['super_attribute'] = $params['bss_super_attribute'][$row];
                $paramsTableOrdering['product'] = $params['product'];
                $paramsTableOrdering['options'] = isset($params['options']) ? $params['options'] : [];
                if (!empty($this->options)) {
                    $paramsTableOrdering['options'] = $this->options;
                } else {
                    $paramsTableOrdering['options'] = $paramsTableOrdering['options'];
                }
                $paramsTableOrdering['selected_configurable_option'] = $params['selected_configurable_option'];
                $this->getRequest()->setParam('qty', $qty);
                $this->cart->addProduct($product, $paramsTableOrdering);
                $this->getCustomOption($product);
                $success = true;
            }
        }
        return $success;
    }

    /**
     * @param mixed $e
     * @return string
     */
    private function catchException($e)
    {
        if ($this->_checkoutSession->getUseNotice(true)) {
            $this->messageManager->addNoticeMessage(
                $this->escaper->escapeHtml($e->getMessage())
            );
        } else {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                $this->messageManager->addErrorMessage(
                    $this->escaper->escapeHtml($message)
                );
            }
        }

        $url = $this->_checkoutSession->getRedirectUrl(true);

        if (!$url) {
            $cartUrl = $this->cartHelper->getCartUrl();
            $url = $this->_redirect->getRedirectUrl($cartUrl);
        }
        return $url;
    }

    /**
     * @param $params
     * @param $product
     * @param $qtyDecimal
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateAjax($params, $product, $qtyDecimal)
    {
        if ($this->helperBss->isAjax($product)) {
            $success = $this->addMultipleProductAjax($params, $qtyDecimal);
        } else {
            $success = $this->addMultipleProduct($params, $qtyDecimal);
        }
        return $success;
    }
}
