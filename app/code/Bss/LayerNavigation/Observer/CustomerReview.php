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
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerReview implements ObserverInterface
{
    /**
     * CustomerReview constructor.
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->productloader = $productloader;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $review \Magento\Review\Model\Review */
        $review = $observer->getEvent()->getObject();
        if ($review->isApproved()) {
            $product = $this->productloader->create()->load($review->getEntityPkValue());
            if ($product->getId()) {
                try {
                    $this->reviewFactory->create()->getEntitySummary($product, $review->getStoreId());
                    $product->setRating($product->getRatingSummary()->getRatingSummary());
                    $product->save();
                } catch (\Exception $e) {
                     $this->logger->critical($e);
                }
            }
        }
    }
}
