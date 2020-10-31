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
 * @package    Bss_PreOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PreOrder\Plugin;

use Bss\PreOrder\Model\Attribute\Source\Order;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

class CartNotice
{
    /**
     * @var \Bss\PreOrder\Helper\Data
     */
    protected $helper;

    /**
     * CartNotice constructor.
     * @param \Bss\PreOrder\Helper\Data $helper
     */
    public function __construct(
        \Bss\PreOrder\Helper\Data $helper,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
        $this->helper=$helper;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
    }

    /**
     * @param $subject
     * @param $item
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetItemHtml($subject, $item)
    {
        if ($this->helper->isEnable()) {
            if ($item->getProductType()=='configurable') {
                $productId = $item->getOptionByCode('simple_product')->getProduct()->getId();
            } else {
                $productId = $item->getProductId();
            }
            $product = $this->helper->getProductItem($productId);
            $isInStock = $product->isSaleable();
            $preOrder = $product->getData('preorder');
            $note = false;
            $message = $this->helper->replaceVariableX(
                $this->helper->getNote(),
                $this->helper->formatDate($product->getData('restock'))
            );
            if ($this->helper->isPreOrder($preOrder, $isInStock)) {
                $note = true;
            }
            if ($preOrder==Order::ORDER_OUT_OF_STOCK && $isInStock) {
                $stock = $this->helper->getStockItem($productId)->getQty();
                if ($this->helper->checkVersion()) {
                    $stockData = $this->getSalableQuantityDataBySku->execute($product->getSku());
                    $stock = $stockData["0"]["qty"];
                }
                if ($item->getQty()>$stock) {
                    $note = true;
                }
            }
            if ($note) {
                return [$item->setMessage($message)];
            }
        }
        return [$item];
    }
}
