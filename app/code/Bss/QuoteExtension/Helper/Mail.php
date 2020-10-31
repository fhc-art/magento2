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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\QuoteExtension\Helper;

use Bss\QuoteExtension\Model\Config\Source\Status;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Mail
 *
 * @package Bss\QuoteExtension\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PATH_REQUEST4QUOTE_EMAIL_IDENTITY = 'bss_request4quote/request4quote_email_config/sender_email_identity';
    const PATH_REQUEST4QUOTE_EMAIL_COPY = 'bss_request4quote/request4quote_email_config/send_email_copy';
    const PATH_REQUEST4QUOTE_NEW_QUOTE = 'bss_request4quote/request4quote_email_config/new_quote_extension';
    const PATH_REQUEST4QUOTE_NEW_QUOTE_CUSTOMER = 'bss_request4quote/request4quote_email_config/new_quote_extension_customer';
    const PATH_REQUEST4QUOTE_RECEIVE_EMAIL = 'bss_request4quote/request4quote_email_config/receive_email_identity';
    const PATH_REQUEST4QUOTE_QUOTE_ACCEPT = 'bss_request4quote/request4quote_email_config/quote_extension_accept';
    const PATH_REQUEST4QUOTE_CANCELLED = 'bss_request4quote/request4quote_email_config/quote_extension_cancelled';
    const PATH_REQUEST4QUOTE_QUOTE_REJECTED = 'bss_request4quote/request4quote_email_config/quote_extension_rejected';
    const PATH_REQUEST4QUOTE_QUOTE_EXPIRED = 'bss_request4quote/request4quote_email_config/quote_extension_expired';
    const PATH_REQUEST4QUOTE_QUOTE_ORDERED = 'bss_request4quote/request4quote_email_config/quote_extension_ordered';
    const PATH_REQUEST4QUOTE_QUOTE_RESUBMIT = 'bss_request4quote/request4quote_email_config/quote_extension_resubmit';

    /**
     * @var array
     */
    protected $parentProductTypeList = ['configurable', 'grouped'];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var FormatDate
     */
    protected $emailData;

    /**
     * @var HidePriceEmail
     */
    protected $hidePriceEmail;

    /**
     * Mail constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param Data $helper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param FormatDate $emailData
     * @param HidePriceEmail $hidePriceEmail
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        Data $helper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        FormatDate $emailData,
        \Bss\QuoteExtension\Helper\HidePriceEmail $hidePriceEmail
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManagerInterface;
        $this->helper = $helper;
        $this->layout = $layout;
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager    = $messageManager;
        $this->transportBuilder = $transportBuilder;
        $this->senderResolver = $senderResolver;
        $this->emailData = $emailData;
        $this->hidePriceEmail = $hidePriceEmail;
    }

    /**
     * Get Sender Email
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailSender()
    {
        $from = $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_EMAIL_IDENTITY,
            ScopeInterface::SCOPE_STORE
        );
        $result = $this->senderResolver->resolve($from);
        return $result['email'];
    }

    /**
     * Get Sender Name
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailSenderName()
    {
        $from = $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_EMAIL_IDENTITY,
            ScopeInterface::SCOPE_STORE
        );
        $result = $this->senderResolver->resolve($from);
        return $result['name'];
    }

    /**
     * Get Email copy to
     *
     * @return array
     */
    public function getEmailCoppy()
    {
        $sendEmailCoppys = $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_EMAIL_COPY,
            ScopeInterface::SCOPE_STORE
        );
        if ($sendEmailCoppys != '') {
            return $this->helper->toArray($sendEmailCoppys);
        }
        return [];
    }

    /**
     * Get email for new quote config
     *
     * @return mixed
     */
    public function getEmailNewQuote()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_NEW_QUOTE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for new quote config
     *
     * @return mixed
     */
    public function getEmailNewQuoteForCustomer()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_NEW_QUOTE_CUSTOMER,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get email for receive quote config
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailReceiveEmail()
    {
        $from = $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_RECEIVE_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
        $result = $this->senderResolver->resolve($from);
        return $result['email'];
    }

    /**
     * Get email for receive quote config
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getEmailReceiveEmailName()
    {
        $from = $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_RECEIVE_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
        $result = $this->senderResolver->resolve($from);
        return $result['name'];
    }

    /**
     * Get email for cancel quote config
     *
     * @return mixed
     */
    public function getEmailCancelledQuote()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_CANCELLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for reject quote config
     *
     * @return mixed
     */
    public function getEmailRejectedQuote()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_QUOTE_REJECTED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for resubmit quote config
     *
     * @return mixed
     */
    public function getEmailResubmitQuote()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_QUOTE_RESUBMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for accept quote config
     *
     * @return mixed
     */
    public function getEmailAcceptQuote()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_QUOTE_ACCEPT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for expired quote config
     *
     * @return mixed
     */
    public function getEmailQuoteExpried()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_QUOTE_EXPIRED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for ordered quote config
     *
     * @return mixed
     */
    public function getEmailQuoteOrdered()
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_QUOTE_ORDERED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Send new quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationNewQuoteEmail($quote, $requestQuote)
    {
        $templateName = $this->getEmailNewQuote();
        $senderEmail = $quote->getCustomerEmail();
        if ($senderEmail) {
            $senderName  = is_string(__('Customer ')) ? __('Customer ') : __('Customer ')->getText();
            $recipientEmail = $this->getEmailReceiveEmail();
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'created_at'   => $this->emailData->getCreatedAtFormatted(
                    $quote->getCreatedAt(),
                    $quote->getstore(),
                    \IntlDateFormatter::MEDIUM
                ),
                'quote'        => $quote
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send new quote email for customer
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationNewQuoteEmailForCustomer($quote, $requestQuote)
    {
        $templateName = $this->getEmailNewQuoteForCustomer();
        $senderEmail = $quote->getCustomerEmail();
        if ($requestQuote->getStatus() === Status::STATE_PENDING
            || $requestQuote->getStatus() === Status::STATE_CANCELED
            || $requestQuote->getStatus() === Status::STATE_REJECTED
        ) {
            foreach ($quote->getAllVisibleItems() as $item) {
                /* @var $item \Magento\Quote\Model\Quote\Item */
                $product = $item->getProduct();
                $item->setNeedCheckPrice(true);
                $item->setProduct($product);
                if ($item->getProductType() == 'configurable') {
                    $parentProductId = $item->getProductId();
                    $childProductSku = $item->getSku();
                    $canShowPrice = $this->hidePriceEmail->canShowPrice($parentProductId, $childProductSku);
                } else {
                    $canShowPrice = $this->hidePriceEmail->canShowPrice($item->getProductId(), false);
                }
                if (!$canShowPrice) {
                    $quote->setNeedHidePrice(true);
                    break;
                }
            }
        }
        if ($senderEmail) {
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $quote->getCustomerEmail();
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'quote'        => $quote
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send accept quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationAcceptQuoteEmail($quote, $requestQuote)
    {
        $templateName = $this->getEmailAcceptQuote();
        $senderEmail     = $this->getEmailSender();

        if ($senderEmail) {
            $recipientEmail = $quote->getCustomerEmail();
            $requestQuoteUrl = $this->_getUrl(
                "quoteextension/quote/view",
                [
                    'quote_id' => $requestQuote->getId(),
                    'token' => $requestQuote->getToken()
                ]
            );

            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'created_at'   => $this->emailData->getCreatedAtFormatted(
                    $quote->getCreatedAt(),
                    $quote->getstore(),
                    \IntlDateFormatter::MEDIUM
                ),
                'request_url' => $requestQuoteUrl,
                'requestQuote' => $requestQuote,
                'quote'        => $quote
            ];

            $storeId     = $this->storeManager->getStore()->getId();
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send cancel quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationQuoteCancelledEmail($quote, $requestQuote)
    {
        $currentDate = $this->emailData->getCurrentDate();
        $cancelDate = $this->emailData->getCreatedAtFormatted(
            $currentDate,
            $quote->getstore(),
            \IntlDateFormatter::MEDIUM
        );
        $templateName = $this->getEmailCancelledQuote();
        $senderEmail = $quote->getCustomerEmail();

        if ($senderEmail) {
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $quote->getCustomerEmail();
            $recipientName  = $this->helper->getCustomerName($quote->getCustomerId());
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'cancelled_date'   => $cancelDate,
                'customer_name' => $recipientName,
                'quote'        => $quote
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send ordered quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationQuoteOrderedEmail($quote, $requestQuote)
    {
        $templateName = $this->getEmailQuoteOrdered();
        $senderEmail = $quote->getCustomerEmail();

        if ($senderEmail) {
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $this->getEmailReceiveEmail();
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'quote'        => $quote
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send reject quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationQuoteRejectedEmail($quote, $requestQuote)
    {
        $currentDate = $this->emailData->getCurrentDate();
        $cancelDate = $this->emailData->getCreatedAtFormatted(
            $currentDate,
            $quote->getstore(),
            \IntlDateFormatter::MEDIUM
        );
        $templateName = $this->getEmailRejectedQuote();
        $senderEmail = $quote->getCustomerEmail();

        if ($requestQuote->getStatus() === Status::STATE_PENDING
            || $requestQuote->getStatus() === Status::STATE_CANCELED
            || $requestQuote->getStatus() === Status::STATE_REJECTED
        ) {
            foreach ($quote->getAllVisibleItems() as $item) {
                /* @var $item \Magento\Quote\Model\Quote\Item */
                $product = $item->getProduct();
                $item->setNeedCheckPrice(true);
                $item->setProduct($product);
                if ($item->getProductType() == 'configurable') {
                    $parentProductId = $item->getProductId();
                    $childProductSku = $item->getSku();
                    $canShowPrice = $this->hidePriceEmail->canShowPrice($parentProductId, $childProductSku);
                } else {
                    $canShowPrice = $this->hidePriceEmail->canShowPrice($item->getProductId(), false);
                }
                if (!$canShowPrice) {
                    $quote->setNeedHidePrice(true);
                    break;
                }
            }
        }

        $requestQuoteUrl = $this->_getUrl(
            "quoteextension/quote/view",
            [
                'quote_id' => $requestQuote->getId(),
                'token' => $requestQuote->getToken()
            ]
        );

        if ($senderEmail) {
            $senderName  = is_string(__('Admin ')) ? __('Admin ') : __('Admin ')->getText();
            $recipientEmail = $quote->getCustomerEmail();
            $recipientName  = $this->helper->getCustomerName($requestQuote->getCustomerId());
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'cancelled_date' => $cancelDate,
                'customer_name' => $recipientName,
                'quote' => $quote,
                'request_quote' => $requestQuote,
                'request_url' => $requestQuoteUrl
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send resubmit quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationQuoteReSubmitEmail($quote, $requestQuote)
    {
        $templateName = $this->getEmailResubmitQuote();
        $senderEmail = $quote->getCustomerEmail();
        $updateAt = $requestQuote->getUpdatedAt();
        if ($senderEmail) {
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $this->getEmailReceiveEmail();
            $recipientName  = $this->helper->getCustomerName($requestQuote->getCustomerId());
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'update_date'   => $updateAt,
                'customer_name' => $recipientName,
                'quote'        => $quote
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);
            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send reminder quote email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendNotificationQuoteReminderEmail($quote, $requestQuote)
    {
        $templateName = $this->getEmailResubmitQuote();
        $senderEmail = $quote->getCustomerEmail();
        $updateAt = $requestQuote->getUpdatedAt();
        if ($senderEmail) {
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $this->getEmailReceiveEmail();
            $recipientName  = $this->helper->getCustomerName($requestQuote->getCustomerId());
            $variables      = [
                'increment_id' => $requestQuote->getIncrementId(),
                'expired_day'   => $updateAt,
                'customer_name' => $recipientName,
                'quote'        => $quote
            ];
            $storeId     = $this->storeManager->getStore()->getId();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);

            /* Send Email Reminder To Admin */
            $recipientEmail[] = $this->getEmailReceiveEmail();

            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Send Expired Email
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Bss\QuoteExtension\Model\ManageQuote $requestQuote
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendNotificationExpiredEmail($quote, $requestQuote)
    {
        $templateName = $this->getEmailQuoteExpried();
        $senderEmail     = $this->getEmailSender();

        if ($senderEmail) {
            $recipientEmail = $quote->getCustomerEmail();
            $url = $this->storeManager->getStore()->getUrl();

            if ($requestQuote->getStatus() === Status::STATE_PENDING
                || $requestQuote->getStatus() === Status::STATE_CANCELED
                || $requestQuote->getStatus() === Status::STATE_REJECTED
            ) {
                foreach ($quote->getAllVisibleItems() as $item) {
                    /* @var $item \Magento\Quote\Model\Quote\Item */
                    $product = $item->getProduct();
                    $item->setNeedCheckPrice(true);
                    $item->setProduct($product);
                    if ($item->getProductType() == 'configurable') {
                        $parentProductId = $item->getProductId();
                        $childProductSku = $item->getSku();
                        $canShowPrice = $this->hidePriceEmail->canShowPrice($parentProductId, $childProductSku);
                    } else {
                        $canShowPrice = $this->hidePriceEmail->canShowPrice($item->getProductId(), false);
                    }
                    if (!$canShowPrice) {
                        $quote->setNeedHidePrice(true);
                        break;
                    }
                }
            }

            $variables      = [
                'increment_id'  => $requestQuote->getIncrementId(),
                'created_at'    => $this->emailData->getCreatedAtFormatted(
                    $quote->getCreatedAt(),
                    $quote->getstore(),
                    \IntlDateFormatter::MEDIUM
                ),
                'quote'         => $quote,
                'purchase_link' => $url,
                'expired_at'    => $this->emailData->formatDate($requestQuote->getExpiry(), \IntlDateFormatter::SHORT)
            ];

            $storeId     = $this->storeManager->getStore()->getId();
            $senderName  = $this->getEmailSenderName();
            $recipientEmail = $this->getRecipientsEmail($recipientEmail);

            /* Send Email Expired To Admin */
            $recipientEmail[] = $this->getEmailReceiveEmail();

            $this->send(
                $templateName,
                $senderName,
                $senderEmail,
                $recipientEmail,
                $variables,
                $storeId
            );
        }
    }

    /**
     * Get other email to sender
     *
     * @param string||array $recipientEmail
     * @return array
     */
    protected function getRecipientsEmail($recipientEmail)
    {
        $emailCoppys = $this->getEmailCoppy();
        if (!empty($emailCoppys)) {
            $emailCoppys[] = $recipientEmail;
            $receivers = $emailCoppys;
            return $receivers;
        }

        return $recipientEmail;
    }

    /**
     * Send Notification Email
     *
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string||array $recipientEmail
     * @param array $variables
     * @param int $storeId
     * @return bool
     */
    protected function send(
        $templateName,
        $senderName,
        $senderEmail,
        $recipientEmail,
        $variables,
        $storeId
    ) {
        $this->inlineTranslation->suspend();
        try {
            if (is_array($recipientEmail)) {
                foreach ($recipientEmail as $recipient) {
                    $this->_send(
                        $templateName,
                        $senderName,
                        $senderEmail,
                        $recipient,
                        $variables,
                        $storeId
                    );
                }
            } else {
                $this->_send(
                    $templateName,
                    $senderName,
                    $senderEmail,
                    $recipientEmail,
                    $variables,
                    $storeId
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t send the email quote right now.'));
        }

        $this->inlineTranslation->resume();
        return true;
    }

    /**
     * Send Notification Email
     *
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param array $variables
     * @param int $storeId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function _send(
        $templateName,
        $senderName,
        $senderEmail,
        $recipientEmail,
        $variables,
        $storeId
    ) {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateName)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId,
            ])
            ->setTemplateVars($variables)
            ->setFrom([
                'name'  => $senderName,
                'email' => $senderEmail
            ])
            ->addTo($recipientEmail)
            ->setReplyTo($senderEmail)
            ->getTransport();

        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
