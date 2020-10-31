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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_OrderAmount
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\OrderAmount\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 *
 * @package Bss\OrderAmount\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    protected $cartHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $currency;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Bss\OrderAmount\Helper\Data $helper
     * @param \Magento\Framework\Locale\CurrencyInterface $currency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Bss\OrderAmount\Helper\Data $helper,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->currency = $currency;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function execute()
    {
        $result = [];

        if ($this->isDisabled()) {
            $this->messageManager->addNoticeMessage($this->getMessage());
            $result['message'] = $this->getMessage();
        } else {
            $result['success'] = $this->checkoutSession->getQuote()->validateMinimumAmount();
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    protected function getMessage()
    {
        $message = $this->helper->getMessage();

        if (empty($message)) {
            $minimumAmount = $this->helper->getAmoutDataForCustomerGroup();
            if (!$minimumAmount) {
                $minimumAmount = 0;
            }

            $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $minimumAmount = $this->currency->getCurrency($currencyCode)->toCurrency($minimumAmount);
            $message = __('Minimum order amount is %1', $minimumAmount);
        } else {
            $message = __($message);
        }

        return $message;
    }

    /**
     * @return bool
     */
    protected function isDisabled()
    {
        return !$this->checkoutSession->getQuote()->validateMinimumAmount();
    }
}
