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

namespace Bss\ConfigurableProductWholesale\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class LoadItem
 *
 * @package Bss\ConfigurableProductWholesale\Controller\Index
 */
class LoadItem extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Bss\ConfigurableProductWholesale\Helper\Data
     */
    private $helperBss;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @param Action\Context $context
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param EncoderInterface $jsonEncoder
     * @param \Bss\ConfigurableProductWholesale\Helper\Data $helperBss
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     */
    public function __construct(
        Action\Context $context,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        EncoderInterface $jsonEncoder,
        \Bss\ConfigurableProductWholesale\Helper\Data $helperBss,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        parent::__construct($context);
        $this->helperBss = $helperBss;
        $this->checkoutSession = $checkoutSession;
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
    }

    /**
     * Get qty item in cart when edit product
     *
     * @return mixed
     */
    public function execute()
    {
        try {
            $itemId = $this->getRequest()->getParam('item_id');
            $productId = $this->getRequest()->getParam('product');
            $this->validateController($itemId, $productId);
            $quote = $this->checkoutSession->create()->getQuote();
            $itemCurrent = $this->getCurrentItem($quote, $itemId);
            if (!isset($itemCurrent)) {
                throw new \Exception(__("Cannot load item"));
            }
            $optionsCurrent = $this->_getOptionProduct($itemCurrent);
            $customOptionsCurrent = $this->_getCustomOption($optionsCurrent);
            $productApply = [];
            $childApply = [];
            $respon = [];
            $items = $quote->getAllItems();
            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $productId) {
                    $options = $this->_getOptionProduct($item);
                    if ($this->_checkItem($options, $customOptionsCurrent)) {
                        $productApply[$item->getId()] = [
                            'qty' => $item->getQty(),
                            'data' => $this->_getOptionData($options)
                        ];
                    }
                } else {
                    $parentItem = $item->getParentItem();
                    if (isset($parentItem) && $parentItem->getProduct()->getId() == $productId) {
                        $childApply[$parentItem->getId()] = $item->getProduct()->getId();
                    }
                }
            }
            foreach ($productApply as $id => $data) {
                $productId = $childApply[$id];
                $respon['product'][$productId] = $data;
                $respon['item'][$productId] = $id;
            }
            $respon['default'] = $childApply[$itemId];
            return $this->getResponse()->setBody(
                $this->jsonEncoder->encode($respon)
            );
        } catch (\Exception $e) {
            return false;
        }
    }

    private function validateController($itemId, $productId)
    {
        if (!($this->helperBss->getConfig() && isset($itemId) && isset($productId))) {
            throw new \Exception(__("Cannot load item"));
        }
    }

    private function getCurrentItem($quote, $itemId)
    {
        $result = $quote->getItemById($itemId);
        if (!$result) {
            throw new \Exception(__("Cannot load item"));
        }
        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return mixed
     */
    private function _getOptionProduct($item)
    {
        if ($item) {
            $product = $item->getProduct();
            return $product->getTypeInstance()->getOrderOptions($product);
        }
        return false;
    }

    /**
     * @param array $options
     * @param array $customOptionsCurrent
     * @return bool
     */
    private function _checkItem($options, $customOptionsCurrent)
    {
        if (!isset($options['options'])) {
            return empty($customOptionsCurrent);
        }
        if (count($options['options']) !== count($customOptionsCurrent)) {
            return false;
        }
        foreach ($options['options'] as $key => $option) {
            $result = array_diff($option, $customOptionsCurrent[$key]);
            if (isset($result) && !empty($result)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $optionsCurrent
     * @return array
     */
    private function _getCustomOption($optionsCurrent)
    {
        $customOptionsCurrent = [];
        if (isset($optionsCurrent['options'])) {
            $customOptionsCurrent = $optionsCurrent['options'];
        }
        return $customOptionsCurrent;
    }

    /**
     * @param array $options
     * @return array
     */
    private function _getOptionData($options)
    {
        $option = [];
        if (isset($options['attributes_info']) && !empty($options['attributes_info'])) {
            foreach ($options['attributes_info'] as $attr) {
                $optionId = $attr['option_id'];
                $option['data-option-'.$optionId] = $attr['option_value'];
            }
        }
        return $option;
    }
}
