<?php

namespace Bss\CustomizeCreditLimit\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ActivePaymentMethod implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * ActivePaymentMethod constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        try {
            $product =  $this->productRepository->get(\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU);
            $result = $observer->getResult();
            $method = $observer->getMethodInstance()->getCode();
            $checkProduct = $this->checkoutSession->getQuote()->getItemByProduct($product);

            if ($checkProduct && $method != 'authnetcim') {
                $result->setData('is_available', false);
            }
        } catch (\Exception $exception) {
            $this->logger->critical('Bss_CustomCreditLimit : '.$exception->getMessage());
        }
    }
}
