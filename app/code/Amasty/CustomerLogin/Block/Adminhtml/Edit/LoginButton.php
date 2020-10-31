<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Block\Adminhtml\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class LoginButton extends GenericButton implements ButtonProviderInterface
{
    const ADMIN_RESOURCE = 'Amasty_CustomerLogin::admin_login';

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    private $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var bool
     */
    private $isAllowed = true;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
    ) {
        parent::__construct($context, $registry);
        $this->isAllowed = $context->getAuthorization()->isAllowed(self::ADMIN_RESOURCE);
        $this->customerRepository = $customerRepository;
        $this->storeManager = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->isAllowed && $customerId = $this->getCustomerId()) {
            if ($this->scopeConfig->getValue('customer/account_share/scope')) {
                $data = [
                    'label' => __('Login as Customer'),
                    'class' => 'amasty-customer-login',
                    'on_click' =>
                        sprintf("window.open('%s', '_blank');", $this->getLoginLink()),
                    'sort_order' => 62,
                ];
            } else {
                try {
                    $customerWebsite = (int)$this->customerRepository->getById($customerId)->getWebsiteId();

                    $data = [
                        'label' => __('Login as Customer'),
                        'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
                        'button_class' => 'amasty-customer-login',
                        'options' => []
                    ];

                    foreach ($this->storeManager->getWebsites() as $website) {
                        $data['options'][] = [
                            'label' => __('Login on Website "%1"', $website->getName()),
                            'default' => (int)$website->getId() === $customerWebsite,
                            'onclick' =>
                                sprintf("window.open('%s', '_blank');", $this->getLoginLink($website->getId())),
                            'sort_order' => 62,
                        ];
                    }
                } catch (\Exception $e) {
                    null;
                }
            }
        }
        return $data;
    }

    public function getLoginLink($website = false)
    {
        $data = ['customer_id' => $this->getCustomerId()];

        if ($website !== false) {
            $data['website'] = (int)$website;
        }

        return $this->getUrl('amcustomerlogin/customer/login', $data);
    }
}
