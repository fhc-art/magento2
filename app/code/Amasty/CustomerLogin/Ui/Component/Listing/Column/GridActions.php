<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Ui\Component\Listing\Column;

use Magento\Customer\Ui\Component\Listing\Column\Actions;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class GridActions extends Actions
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $urlBuilder, $components, $data);
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        if ($this->scopeConfig->getValue('customer/account_share/scope')) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['login'] = [
                    'href' => $this->getLoginLink($item['entity_id']),
                    'target' => '_blank',
                    'label' => __('Login as Customer')
                ];
            }
        } else {
            foreach ($dataSource['data']['items'] as &$item) {
                foreach ($this->storeManager->getWebsites() as $website) {
                    $item[$this->getData('name')]['login_' . $website->getId()] = [
                        'href' => $this->getLoginLink($item['entity_id'], $website->getId()),
                        'target' => '_blank',
                        'label' => __('Login on Website "%1"', $website->getName())
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param int $customerId
     * @param bool|int $website
     *
     * @return string
     */
    private function getLoginLink($customerId, $website = false)
    {
        $data = ['customer_id' => $customerId];

        if ($website !== false) {
            $data['website'] = (int)$website;
        }

        return $this->urlBuilder->getUrl('amcustomerlogin/customer/login', $data);
    }
}
