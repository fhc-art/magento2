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
namespace Bss\QuoteExtension\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ApplyAddToQuoteCollection
 *
 * @package Bss\QuoteExtension\Observer
 */
class ApplyAddToQuoteCollection implements ObserverInterface
{
    /**
     * @var \Bss\QuoteExtension\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ApplyAddToQuoteCollection constructor.
     * @param \Bss\QuoteExtension\Helper\Data $helper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Bss\QuoteExtension\Helper\Data $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->helper=$helper;
        $this->productRepository = $productRepository;
    }

    /**
     * Set Request4quote for product in collection
     *
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            $data = $product->getData();
            if (!isset($data['url_key'])) {
                continue;
            }
            $sku = $product->getSku();
            $productRepository = $this->productRepository->get($sku);
            if ($this->helper->isEnable() && $this->helper->isActiveRequest4Quote($productRepository)) {
                $product->setIsInCollection(true);
                $product->setIsActiveRequest4Quote(true);
            }
        }
        return $this;
    }
}
