<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\StorePickup\Model\Multishipping;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magento\Directory\Model\AllowedCountries;

class Multishipping extends \Magento\Multishipping\Model\Checkout\Type\Multishipping
{
    /**
     * Quote shipping addresses items cache
     *
     * @var array
     */
    protected $_quoteShippingAddressesItems;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Payment\Model\Method\SpecificationInterface
     */
    protected $paymentSpecification;

    /**
     * Initialize dependencies.
     *
     * @var \Magento\Multishipping\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrder
     */
    protected $quoteAddressToOrder;

    /**
     * @var \Magento\Quote\Model\Quote\Item\ToOrderItem
     */
    protected $quoteItemToOrderItem;

    /**
     * @var \Magento\Quote\Model\Quote\Payment\ToOrderPayment
     */
    protected $quotePaymentToOrderPayment;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $quoteAddressToOrderAddress;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var AllowedCountries
     */
    private $allowedCountryReader;

    /**
     * @var \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentProcessor
     */
    private $shippingAssignmentProcessor;

    /**
     * Constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Quote\Model\Quote\AddressFactory $addressFactory
     * @param \Magento\Quote\Model\Quote\Address\ToOrder $quoteAddressToOrder
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     * @param \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Payment\Model\Method\SpecificationInterface $paymentSpecification
     * @param \Magento\Multishipping\Helper\Data $helper
     * @param OrderSender $orderSender
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param array $data
     * @param \Magento\Quote\Api\Data\CartExtensionFactory|null $cartExtensionFactory
     * @param AllowedCountries|null $allowedCountryReader
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Session\Generic $session,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        \Magento\Quote\Model\Quote\Address\ToOrder $quoteAddressToOrder,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress,
        \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment,
        \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Payment\Model\Method\SpecificationInterface $paymentSpecification,
        \Magento\Multishipping\Helper\Data $helper,
        OrderSender $orderSender,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        array $data = [],
        \Magento\Quote\Api\Data\CartExtensionFactory $cartExtensionFactory = null,
        AllowedCountries $allowedCountryReader = null
    ) {
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_session = $session;
        $this->_addressFactory = $addressFactory;
        $this->_storeManager = $storeManager;
        $this->paymentSpecification = $paymentSpecification;
        $this->helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->addressRepository = $addressRepository;
        $this->orderSender = $orderSender;
        $this->priceCurrency = $priceCurrency;
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->quoteAddressToOrder = $quoteAddressToOrder;
        $this->quoteItemToOrderItem = $quoteItemToOrderItem;
        $this->quotePaymentToOrderPayment = $quotePaymentToOrderPayment;
        $this->quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->totalsCollector = $totalsCollector;
        $this->cartExtensionFactory = $cartExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Quote\Api\Data\CartExtensionFactory::class);
        $this->allowedCountryReader = $allowedCountryReader ?: ObjectManager::getInstance()
            ->get(AllowedCountries::class);
        parent::__construct($checkoutSession, $customerSession, $orderFactory, $addressRepository, $eventManager, $scopeConfig, $session, $addressFactory, $quoteAddressToOrder, $quoteAddressToOrderAddress, $quotePaymentToOrderPayment, $quoteItemToOrderItem, $storeManager, $paymentSpecification, $helper, $orderSender, $priceCurrency, $quoteRepository, $searchCriteriaBuilder, $filterBuilder, $totalsCollector, $data, $cartExtensionFactory, $allowedCountryReader);
        $this->_init();
    }

    /**
     * Create orders per each quote address
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     * @throws \Exception
     */
    public function createOrders()
    {
        $orderIds = [];
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = [];

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }

        try {
            foreach ($shippingAddresses as $address) {
                $order = $this->_prepareOrder($address);
                $order->setPickupDate($address->getPickupDate());
                $order->setPickupStore($address->getPickupStore());
                $orders[] = $order;
                $this->_eventManager->dispatch(
                    'checkout_type_multishipping_create_orders_single',
                    ['order' => $order, 'address' => $address, 'quote' => $this->getQuote()]
                );
            }
            foreach ($orders as $order) {
                $order->place();
                $order->save();
                if ($order->getCanSendNewEmailFlag()) {
                    $this->orderSender->send($order);
                }
                $orderIds[$order->getId()] = $order->getIncrementId();
            }
            $this->_session->setOrderIds($orderIds);
            $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId());

            $this->getQuote()->setIsActive(false);
            $this->quoteRepository->save($this->getQuote());

            $this->_eventManager->dispatch(
                'checkout_submit_all_after',
                ['orders' => $orders, 'quote' => $this->getQuote()]
            );

            return $this;
        } catch (\Exception $e) {
            $this->_eventManager->dispatch('checkout_multishipping_refund_all', ['orders' => $orders]);
            throw $e;
        }
    }

}
