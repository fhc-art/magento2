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
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Bss\B2bRegistration\Helper\Data;
use Psr\Log\LoggerInterface;
use Bss\B2bRegistration\Model\Config\Source\CustomerAttribute;

class UpgradeStatusObserver implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * UpgradeStatusObserver constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param Data $helper
     * @param LoggerInterface $logger
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Data $helper,
        LoggerInterface $logger,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
    }

    /**
     * Set Normal status to normal account
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnable()) {
            try {
                $customer = $observer->getCustomer();
                $customer->setCustomAttribute("b2b_activasion_status", CustomerAttribute::NORMAL_ACCOUNT);
                $this->customerSession->create()->setBssSaveAccount('true');
                $this->customerRepository->save($customer);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }
}
