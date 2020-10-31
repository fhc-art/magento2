<?php
namespace Tbi\CustomerIntegration\Controller\Index;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ZendClientFactory;

class Api extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    /** @var ZendClientFactory */
    protected $httpClientFactory;
    /** @var ScopeConfigInterface */
    protected $config;
    /** @var GroupRepositoryInterface */
    protected $customerGroupRepository;

    protected $customerRepository;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        ZendClientFactory $httpClientFactory,
        ScopeConfigInterface $config,
        GroupRepositoryInterface $customerGroupRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->httpClientFactory       = $httpClientFactory;
        $this->config                  = $config;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->logger                  = $logger;
        $this->resultPageFactory       = $resultPageFactory;
        $this->addressRepository       = $addressRepository;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Get TBI URI from config. Skip observer if not set
        $tbiUri = 'http://207.106.217.102:8181'; // TODO: remove and replace with following line
        // $tbiUri = $this->config->getValue(self::CONFIG_PATH_TBI_URI, ScopeInterface::SCOPE_STORE);
        if (!$tbiUri) {
            return;
        }

        // $customer_id   = 19;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerData  = $objectManager->create('Magento\Customer\Model\Customer')->load($customer_id);
        $this->updateCustomerAttribute($customer_id, 'ca_api_data', '1');

        foreach ($customerData->getAddresses() as $address) {
            $customerAddress[] = [
            //      "email"                      => $customerData->getData('email'),
                    "entity_id"                  => $address['entity_id'],
                    "company"                    => $address['company'],
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

      // echo "<pre>";
      // print_r($customerData->getData());
      // print_r($customerAddress);

     

        $tbiUri .= '/restMaintain/customer.maint';

        /** @var ZendClient $client */
        $client = $this->httpClientFactory->create();
        $client->setConfig([
            'maxredirects' => 0,
            'timeout'      => 30,
        ]);

        $postBody = [
//                "mode"                  => "U0", // A = Add; U = Update

			"mode"                  => "U", // A = Add; U = Update
                "b2b_activasion_status" => $customerData->getData('b2b_activasion_status'),
                "firstname"             => $customerData->getData('firstname'),
                "lastname"              => $customerData->getData('lastname'),
                "_email"                => $customerData->getData('email'),
                "ca_resale_number"      => $customerData->getData('ca_resale_number'),
                "industry"              => $customerData->getData('industry'),
                "ca_contractor_number"  => $customerData->getData('ca_contactor_number'), //sic
                "ca_multiple_accounts"  => $customerData->getData('ca_customer_comment'), //sic
                "ca_credit_limit"       => $customerData->getData('ca_credit_limit'),
                "ca_customer_number"    => $customerData->getData('ca_customer_number'),
                "group_id"              => $customerData->getData('group_id'),
                "default_billing"       => $customerData->getData('default_billing'),
                "default_shipping"      => $customerData->getData('default_shipping'),
                "ADDRESSVAL"            => $customerAddress,
            ];
        // echo "<pre>";
        // print_r($postBody);

        // die("ruk");
        $client->setUri($tbiUri);
        $client->setRawData(json_encode(["REC" => $postBody]));

        try {
            echo "<pre>";
            $response = $client->request(\Zend\Http\Request::METHOD_POST);
            print_r($response);
            // die("d");

            $this->logger->info(json_encode($postBody, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
            $this->logger->info($client->getLastRequest());
            $this->logger->info(json_encode($response->getBody(), JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));
            $this->logger->info($client->getLastResponse());
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e->getMessage()]);
        }
        return $this->resultPageFactory->create();
    }

    public function updateCustomerAttribute($customerId, $attributeCode, $value)
    {
        $customerData = $this->customerRepository->getById($customerId);
        $customerData->setCustomAttribute($attributeCode, $value);
        $this->customerRepository->save($customerData);
    }
}
