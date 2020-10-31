<?php

namespace Tbi\CustomerIntegration\Observer\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ZendClientFactory;

class DeleteAfter implements \Magento\Framework\Event\ObserverInterface
{

    const CONFIG_PATH_TBI_URI = 'customerintegration/general/url';

    /** @var ZendClientFactory */
    protected $httpClientFactory;
    /** @var ScopeConfigInterface */
    protected $config;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    private $_objectManager;

    /**
     * @param ZendClientFactory    $httpClientFactory
     * @param ScopeConfigInterface $config
     * @param Logger               $logger
     */
    public function __construct(ZendClientFactory $httpClientFactory, ScopeConfigInterface $config, CustomerRepositoryInterface $customerRepository, \Psr\Log\LoggerInterface $logger, \Magento\Customer\Model\SessionFactory $customerSession, \Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->httpClientFactory  = $httpClientFactory;
        $this->config             = $config;
        $this->customerRepository = $customerRepository;
        $this->logger             = $logger;
        $this->customerSession    = $customerSession;
        $this->_objectManager = $objectmanager;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        // Get TBI URI from config. Skip observer if not set
        // $tbiUri = 'http://207.106.217.102:8181'; // TODO: remove and replace with following line
        $tbiUri = $this->config->getValue(self::CONFIG_PATH_TBI_URI, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$tbiUri) {
            return;
        }
        $tbiUri .= '/restMaintain/customer.maint';

        /** @var Customer $customer */
        $customer   = $observer->getCustomer();
        $email = $customer->getData('email');
        if ($email) {
            $customerData = $this->_objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId(1)->loadByEmail($email);
            $customer_id  = $customerData->getId();
            $customerAddress = [];
            foreach ($customerData->getAddresses() as $address) {
                $customerAddress[] = [
                //  "email"                      => $customerData->getData('email'),
                    "entity_id"                  => $address['entity_id'],
                    "company"                    => $this->cleanAddress($address['company']),
                    "street"                     => $address['street'],
                    "city"                       => $address['city'],
                    "region_id"                  => $address['region_id'],
                    "postcode"                   => $address['postcode'],
                    "country_id"                 => $address['country_id'],
                    "firstname_at_address"       => $address['firstname'],
                    "lastname_at_address"        => $address['lastname'],
                    "telephone"                  => $address['telephone'],
                    "fax"                        => $address['fax'],
                ];
            }

            /** @var ZendClient $client */
            $client = $this->httpClientFactory->create();
            $client->setConfig([
                'maxredirects' => 0,
                'timeout'      => 30,
            ]);

            $i = $customerData->getData('ca_resale_number');
            $j = $customerData->getData('ca_contactor_number'); //sic
            $k = $customerData->getData('ca_customer_comment'); //sic
            if (is_null($i)) {
                $i = ' ';
            }

            if (is_null($j)) {
                $j = ' ';
            }

            if (is_null($k)) {
                $k = ' ';
            }

            $postBody = [
                "mode"                  => "D", // U0 = Update From CustomerSaveAfter Backend Observer
                "b2b_activasion_status" => $customerData->getData('b2b_activasion_status'),
                "firstname"             => $this->clean($customerData->getData('firstname')),
                "lastname"              => $this->clean($customerData->getData('lastname')),
                "_email"                => $customerData->getData('email'),
                "ca_resale_number"      => $i,
                "industry"              => $customerData->getData('industry'),
                "ca_contractor_number"  => $j,
                "ca_multiple_accounts"  => $k,
                "ca_credit_limit"       => $customerData->getData('ca_credit_limit'),
                "ca_customer_number"    => $customerData->getData('ca_customer_number'),
                "group_id"              => $customerData->getData('group_id'),
                "default_billing"       => $customerData->getData('default_billing'),
                "default_shipping"      => $customerData->getData('default_shipping'),
                "ADDRESSVAL"            => $customerAddress,
            ];

            $client->setUri($tbiUri);
            $client->setRawData(json_encode(["REC" => $postBody]));

            try {
                $response = $client->request(\Zend\Http\Request::METHOD_POST);
                $this->logger->info("DeleteAfter");
                $this->logger->info(json_encode($postBody, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
                $this->logger->info($client->getLastRequest());
                $this->logger->info(json_encode($response->getBody(), JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
                $this->logger->info($client->getLastResponse());
                if ($response->getStatus() == '200') {
                    // $this->updateCustomerAttribute($customer_id, 'ca_api_data', '1');
                } else {
                    // $this->updateCustomerAttribute($customer_id, 'ca_api_data', '0');
                }
            } catch (\Exception $e) {
                $this->logger->critical('Error message', ['exception' => $e->getMessage()]);
            }
            return true;
        }
    }

    public function updateCustomerAttribute($customerId, $attributeCode, $value)
    {
        $customerData = $this->customerRepository->getById($customerId);
        $customerData->setCustomAttribute($attributeCode, $value);
        $this->customerRepository->save($customerData);
    }

    public function clean($string)
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function cleanAddress($string)
    {
        return str_replace('&', 'and', $string); // testing removal of &  
    }
}
