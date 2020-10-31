<?php
namespace Bss\CustomizeOrder\Override\Model\Order\Email\Sender;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class InvoiceSender
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InvoiceSender extends \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
{


    protected $registry;

    public function __construct(
        Template $templateContainer,
        InvoiceIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        InvoiceResource $invoiceResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Bss\CheckoutCustomField\Helper\Data $helper,
        ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $invoiceResource,
            $globalConfig,
            $eventManager
        );
    }

    /**
     * Sends order invoice email to the customer.
     *
     * Email will be sent immediately in two cases:
     *
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     *
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     *
     * @param Invoice $invoice
     * @param bool $forceSyncMode
     * @return bool
     * @throws \Exception
     */
    public function send(Invoice $invoice, $forceSyncMode = false)
    {
        $invoice->setSendEmail($this->identityContainer->isEnabled());

        $order = $invoice->getOrder();
        $payment = $order->getPayment();
        $methodCode = $payment->getMethod();
        $current_invoice = $this->registry->registry('current_invoice');
        if (!$current_invoice || $methodCode != 'paybycredit') {
            if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
                
                $storeCode = $order->getStore()->getCode();
                $transport = [
                    'order' => $order,
                    'invoice' => $invoice,
                    'comment' => $invoice->getCustomerNoteNotify() ? $invoice->getCustomerNote() : '',
                    'billing' => $order->getBillingAddress(),
                    'payment_html' => $this->getPaymentHtml($order),
                    'store' => $order->getStore(),
                    'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                    'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
                    'bss_custom_field' => $this->helper->getVariableEmailHtml($order->getBssCustomfield(), $storeCode),
                ];

                $this->eventManager->dispatch(
                    'email_invoice_set_template_vars_before',
                    ['sender' => $this, 'transport' => $transport]
                );

                $this->templateContainer->setTemplateVars($transport);

                if ($this->checkAndSend($order)) {
                    $invoice->setEmailSent(true);
                    $this->invoiceResource->saveAttribute($invoice, ['send_email', 'email_sent']);
                    return true;
                }
            }
        } else {
            $invoice->setEmailSent(null);
            $this->invoiceResource->saveAttribute($invoice, 'email_sent');
        }

        $this->invoiceResource->saveAttribute($invoice, 'send_email');

        return false;
    }
}
