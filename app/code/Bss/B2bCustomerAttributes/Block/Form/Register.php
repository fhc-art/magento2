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
 * @package    Bss_B2bCustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Block\Form;

use Magento\Framework\View\Element\Template\Context;
use Bss\CustomerAttributes\Helper\Customerattribute;

class Register extends \Magento\Customer\Block\Form\Register
{
    /**
     * @var Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * @var Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * @var Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var Customerattribute
     */
    protected $customerAttributeHelper;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavAttribute;

    /**
     * @var \Bss\B2bRegistration\Helper\Data
     */
    protected $helper;

    /**
     * Register constructor.
     * @param Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param Customerattribute $customerAttributeHelper
     * @param \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
     * @param \Bss\B2bRegistration\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        Customerattribute $customerAttributeHelper,
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory,
        \Bss\B2bRegistration\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $moduleManager,
            $customerSession,
            $customerUrl,
            $data
        );
        $this->customerAttributeHelper = $customerAttributeHelper;
        $this->eavAttribute = $eavAttributeFactory;
        $this->helper = $helper;
    }
    /**
     *  Get Title Of B2b Account Create Page
     * @return void
     */
    protected function _prepareLayout()
    {
        $title = $this->helper->getTitle();
        if ($title) {
            $this->pageConfig->getTitle()->set(__($title));
        } else {
            $this->pageConfig->getTitle()->set(__('Create New Customer Account'));
        }
    }

    /**
     * @return Customerattribute
     */
    public function getCustomerAttributeHelper()
    {
        return $this->customerAttributeHelper;
    }

    /**
     * @param string $attributeCode
     * @return bool
     */
    public function isAttribureForB2bCustomerAccountCreate($attributeCode)
    {
        $attribute   = $this->eavAttribute->create()
            ->getAttribute('customer', $attributeCode);
        $usedInForms = $attribute->getUsedInForms();        

        if (in_array('b2b_account_create', $usedInForms)) {
            return true;
        }
        return false;
    }    
}
