<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_CreditLimit
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CreditLimit\Block\CreditLimit;
/**
 * Class RemainigCreditLimit
 * @package Ced\CreditLimit\Block\CreditLimit
 */
class RemainigCreditLimit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Ced\CreditLimit\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
             \Magento\Framework\View\Element\Template\Context $context,
             \Magento\Customer\Model\Session $session,
    		\Magento\Framework\Pricing\Helper\Data $pricingHelper,
    		\Ced\CreditLimit\Helper\Data $helper,
             array $data = []                            
    	)
    {
    	$this->pricingHelper = $pricingHelper;
        $this->customerSession = $session;
        $this->helper = $helper;
        parent::__construct($context,$data);
    }

    /**
     * @return mixed
     */
    public function getRemainingCreditLimit()
    {
        $creditCollection = $this->helper->getCustomerCreditLimit($this->customerSession->getCustomerId());
        $formattedPrice = $this->pricingHelper->currency($creditCollection->getRemainingAmount(), true, false);
        return $amount['amount'] = $formattedPrice;
    }
}