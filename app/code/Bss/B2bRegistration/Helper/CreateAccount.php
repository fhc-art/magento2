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
namespace Bss\B2bRegistration\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Helper\Address;
use Magento\Customer\Api\Data\AddressInterfaceFactory;

class CreateAccount extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $context;
    /**
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var RegionInterfaceFactory
     */
    protected $regionDataFactory;

    /**
     * @var Address
     */
    protected $addressHelper;

    /**
     * @var AddressInterfaceFactory
     */
    protected $addressDataFactory;

    /**
     * CreateAccount constructor.
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param FormFactory $formFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param SubscriberFactory $subscriberFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param Address $addressHelper
     * @param AddressInterfaceFactory $addressDataFactory
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        FormFactory $formFactory,
        CustomerRepositoryInterface $customerRepository,
        SubscriberFactory $subscriberFactory,
        RegionInterfaceFactory $regionDataFactory,
        Address $addressHelper,
        AddressInterfaceFactory $addressDataFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->formFactory = $formFactory;
        $this->customerRepository = $customerRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->regionDataFactory = $regionDataFactory;
        $this->addressHelper = $addressHelper;
        $this->addressDataFactory = $addressDataFactory;
    }

    /**
     * @return SessionFactory
     */
    public function getCustomerSessionFactory()
    {
        return $this->customerSession;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return CustomerRepositoryInterface
     */
    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    /**
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriberFactory()
    {
        return $this->subscriberFactory->create();
    }

    /**
     * @return \Magento\Customer\Api\Data\RegionInterface
     */
    public function getRegionDataFactory()
    {
        return $this->regionDataFactory->create();
    }

    /**
     * @return Address
     */
    public function getAddressHelper()
    {
        return $this->addressHelper;
    }

    /**
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getDataAddressFactory()
    {
        return $this->addressDataFactory->create();
    }
}
