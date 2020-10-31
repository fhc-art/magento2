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

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class UpdateItemOptions
 *
 * @package Bss\ConfigurableProductWholesale\Controller\Cart
 */
class UpdateItemOptions extends \Magento\Checkout\Controller\Cart\UpdateItemOptions
{
    /**
     * @var \Bss\ConfigurableProductWholesale\Helper\Data
     */
    private $helperBss;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param mixed $item
     */
    protected $item;

    /**
     * @param \Magento\Quote\Model\Quote\Item
     */
    private $originalQuoteItem;

    /**
     * @var array
     */
    private $waitToAddItems = [];

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param \Bss\ConfigurableProductWholesale\Helper\Data $helperBss
     * @param StockItemRepository $stockItemRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Bss\ConfigurableProductWholesale\Helper\Data $helperBss,
        StockRegistryInterface $stockRegistry,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->helperBss = $helperBss;
        $this->stockRegistry = $stockRegistry;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Update product configuration for a cart item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute($coreRoute = null)
    {
        $params = $this->getRequest()->getParams();
        $stockItem = $this->stockRegistry->getStockItem($params['product'])->getData();

        if (!$this->helperBss->getConfig() || !isset($params['bss-table-ordering'])) {
            return parent::execute($coreRoute);
        }
        try {
            $originItemId = $this->getRequest()->getParam('bss_cpw_item_id');
            $this->originalQuoteItem = $this->cart->getQuote()->getItemById($originItemId);
            if (!$this->originalQuoteItem) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We can\'t find the quote item.')
                );
            }
            $related = $this->getRequest()->getParam('related_product');
            if ($params['bss_cpw_is_ajax']) {
                $this->addMultipleProductAjax($params, $stockItem['is_qty_decimal']);
            } else {
                $this->addMultipleProduct($params, $stockItem['is_qty_decimal']);
            }

            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }
            $this->cart->save();
            $item = $this->item;
            if (isset($item)) {
                $this->_eventManager->dispatch(
                    'checkout_cart_update_item_complete',
                    [
                        'item' => $item,
                        'request' => $this->getRequest(),
                        'response' => $this->getResponse()
                    ]
                );
                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                        $message = __(
                            '%1 was updated in your shopping cart.',
                            $item->getProduct()->getName()
                        );
                        $this->messageManager->addSuccessMessage($message);
                    }
                    return $this->_goBack($this->_url->getUrl('checkout/cart'));
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->catchException($e);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t update the item right now.')
            );
            $this->helperBss->getLogger()->critical($e);
            return $this->_goBack();
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }

    /**
     * Add all product to cart with ajax product
     *
     * @param $params
     * @param $qtyDecimal
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addMultipleProductAjax($params, $qtyDecimal)
    {
        if ($this->validateAjaxParam($params)) {
            $optionsEncode = urldecode($params['bss-addtocart-data']);
            $paramData = $this->helperBss->getJsonHelper()->jsonDecode($optionsEncode);
            $quoteItemData = $this->helperBss->getJsonHelper()->jsonDecode(urldecode($params['bss-updatecart-data']));
            $firstItem = true;
            foreach ($paramData as $productId => $data) {
                $qty = $data['qty'];
                if ($qty < 0 || !isset($data['data'])) {
                    continue;
                }
                $qty = $this->getQty($qty, $qtyDecimal);
                $paramsTableOrdering = [];
                $product = $this->getProduct();
                $paramsTableOrdering['qty'] = $qty;
                $superAttribute = $this->getOption($data);
                $paramsTableOrdering['super_attribute'] = $superAttribute;
                $paramsTableOrdering['options'] = $this->getCustomOption($params);
                $paramsTableOrdering['selected_configurable_option'] = $params['selected_configurable_option'];
                if (!isset($quoteItemData[$productId])) {
                    $this->waitToAddItems[] = $paramsTableOrdering;
                } else {
                    $this->originalQuoteItem = $this->cart->getQuote()->getItemById($quoteItemData[$productId]);
                    $hasFile = $this->getCustomOptionFile($params, $product, $paramsTableOrdering);
                    $id = $quoteItemData[$productId];
                    $quoteItem = $this->cart->getQuote()->getItemById($id);
                    $paramsTableOrdering['id'] = $id;
                    $paramsTableOrdering['product'] = $quoteItem->getProductId();
                    if ($hasFile && !$firstItem) {
                        $this->waitToAddItems[] = $paramsTableOrdering;
                        $this->cart->getQuote()->removeItem($id);
                    } else {
                        $this->updateItems($qty, $id, $paramsTableOrdering);
                        $firstItem = false;
                    }
                    unset($quoteItemData[$productId]);
                }
            }
            $this->removeItem($quoteItemData, $params);
            $this->addNewItems($params);
        }
    }

    /**
     * @param $params
     * @return bool
     */
    private function validateAjaxParam($params)
    {
        return isset($params['bss-updatecart-data'])
                && isset($params['bss-addtocart-data']) && $params['bss-addtocart-data'];
    }

    /**
     * Add all product to cart
     *
     * @param $params
     * @param $qtyDecimal
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addMultipleProduct($params, $qtyDecimal)
    {
        if (!empty($params['bss-qty'])) {
            $product = $this->getProduct($params['product']);
            $firstItem = true;
            foreach ($params['bss-qty'] as $productId => $qty) {
                if ($qty < 0) {
                    continue;
                }
                $qty = $this->getQty($qty, $qtyDecimal);
                $paramsTableOrdering = [];
                $paramsTableOrdering['qty'] = $qty;
                $paramsTableOrdering['super_attribute'] = $params['bss_super_attribute'][$productId];

                $paramsTableOrdering['options'] = $this->_getCustomOption($params);
                $paramsTableOrdering['selected_configurable_option'] = $params['selected_configurable_option'];
                if (!isset($params['bss-item'][$productId])) {
                    $this->waitToAddItems[] = $paramsTableOrdering;
                } else {
                    $id = (int)$params['bss-item'][$productId];
                    $this->originalQuoteItem = $this->cart->getQuote()->getItemById($id);
                    $quoteItem = $this->cart->getQuote()->getItemById($id);
                    $paramsTableOrdering['id'] = $id;
                    $paramsTableOrdering['product'] = $quoteItem->getProductId();
                    $hasFile = $this->getCustomOptionFile($params, $product, $paramsTableOrdering);
                    if ($hasFile && !$firstItem) {
                        $this->waitToAddItems[] = $paramsTableOrdering;
                        $this->cart->getQuote()->removeItem($id);
                    } else {
                        $this->updateItems($qty, $id, $paramsTableOrdering);
                        $firstItem = false;
                    }
                }
            }
            $this->addNewItems($params);
        }
    }

    /**
     * @param $qty
     * @param $qtyDecimal
     * @return array|float|string
     */
    private function getQty($qty, $qtyDecimal)
    {
        if ($qtyDecimal == '0') {
            $qty = floor($qty);
        }
        if (isset($qty) && $qty != 0) {
            $this->helperBss->getLocalFilter()->setOptions(
                ['locale' => $this->helperBss->getLocaleResolver()->getLocale()]
            );
            $qty = $this->helperBss->getLocalFilter()->filter((double)$qty);
        }
        return $qty;
    }

    /**
     * @param $params
     * @return |null
     */
    private function addNewItems($params)
    {
        try {
            foreach ($this->waitToAddItems as $param) {
                if ($param['qty'] == 0) {
                    continue;
                }
                $product = $this->getProduct();
                $this->getCustomOptionFile($params, $product, $param, true);
                $this->cart->addProduct($product, $param);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_goBack();
            return null;
        }
    }

    /**
     * @param int|null $productId
     * @return mixed
     */
    private function getProduct()
    {
        $product = clone $this->originalQuoteItem->getProduct();
        return $product;
    }

    /**
     * @param mixed $e
     * @return string
     */
    private function catchException($e)
    {
        if ($this->_checkoutSession->getUseNotice(true)) {
            $this->messageManager->addNoticeMessage($e->getMessage());
        } else {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                $this->messageManager->addErrorMessage($message);
            }
        }

        $url = $this->_checkoutSession->getRedirectUrl(true);
        if ($url) {
            return $this->resultRedirectFactory->create()->setUrl($url);
        } else {
            $cartUrl = $this->helperBss->getMagentoHelper()->getCartHelper()->getCartUrl();
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->getRedirectUrl($cartUrl)
            );
        }
    }

    /**
     * @param float $qty
     * @param int $id
     * @param array $paramsTableOrdering
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateItems($qty, $id, $paramsTableOrdering)
    {
        try {
            if ($qty == 0) {
                $paramsTableOrdering['qty'] = 1;
                $item = $this->cart->updateItem(
                    $id,
                    $paramsTableOrdering
                );
                $this->cart->getQuote()->deleteItem($item);
            } else {
                $item = $this->cart->updateItem(
                    $id,
                    $paramsTableOrdering
                );
                if (is_string($item)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($item));
                }
                if ($item->getHasError()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
                }
            }
            $this->item = $item;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_goBack();
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function getOption($data)
    {
        $superAttribute = [];
        if (isset($data['data']) && !empty($data['data'])) {
            foreach ($data['data'] as $key => $optionValue) {
                $optionId = str_replace('data-option-', '', $key);
                $superAttribute[$optionId] = $optionValue;
            }
        }
        return $superAttribute;
    }

    /**
     * @param array $params
     * @return array
     */
    private function getCustomOption($params)
    {
        $customOpt = [];
        if (isset($params['options'])) {
            $customOpt = $params['options'];
        }
        return $customOpt;
    }

    /**
     * @param array $params
     * @return array
     */
    private function getCustomOptionFile($params, $product, &$paramsTableOrdering, $cloneOption = false)
    {
        $result = false;
        if ($options = $product->getOptions()) {
            foreach ($options as $option) {
                if ($option->getType() === 'file') {
                    $key = 'options_' . $option->getOptionId() . '_file_action';
                    if (isset($params[$key])) {
                        if ($cloneOption && $this->item) {
                            $fileClone = $this->item->getBuyRequest()->getData('options/' . $option->getOptionId());
                            $paramsTableOrdering[$key] = 'save_old';
                            $paramsTableOrdering['options'][$option->getOptionId()] = $fileClone ? $fileClone : "";
                        } else {
                            $result = true;
                            $paramsTableOrdering[$key] = $params[$key];
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $quoteItemData
     * @param $params
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function removeItem($quoteItemData, $params)
    {
        if (!empty($quoteItemData)) {
            foreach ($quoteItemData as $itemId) {
                $param = $this->originalQuoteItem->getBuyRequest()->getData();
                $param['options'] = $this->getCustomOption($params);
                $param['selected_configurable_option'] = $params['selected_configurable_option'];
                $param['qty'] = 1;
                $product = $this->getProduct();
                if ($this->getCustomOptionFile($params, $product, $param) && !isset($this->item)) {
                    $item = $this->cart->updateItem(
                        $itemId,
                        $param
                    );
                    $this->cart->getQuote()->deleteItem($item);
                    $this->item = $item;
                } else {
                    $this->cart->getQuote()->removeItem($itemId);
                }
            }
        }
    }
}
