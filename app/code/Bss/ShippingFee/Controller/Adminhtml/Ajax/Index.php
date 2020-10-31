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
 * @package    Bss_ShippingFee
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ShippingFee\Controller\Adminhtml\Ajax;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\Registry;

/**
 * Class Index
 *
 * @package Bss\ShippingFee\Controller\Adminhtml\Ajax
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Helper data
     *
     * @var \Bss\ShippingFee\Helper\Data
     */
    protected $helperData;

	/**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

	/**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricing;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Registry
     */
    private $registry;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Bss\ShippingFee\Helper\Data $helperData,
		JsonFactory $resultJsonFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Framework\Pricing\Helper\Data $pricing,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        InvoiceService $invoiceService,
        Registry $registry
	) {
		parent::__construct($context);
		$this->helperData = $helperData;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->orderRepository = $orderRepository;
		$this->pricing = $pricing;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->invoiceService = $invoiceService;
        $this->registry = $registry;
	}

	/**
     * Execute method
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
    	if (!$this->helperData->isEnabled()) {
            return parent::execute();
        }

        $invoiceData = [];
        parse_str($this->getRequest()->getParam('items'), $data);
        if (isset($data['invoice'])) {
            $invoiceData = $data['invoice'];
        }

        $baseShippingFee = (float) $this->getRequest()->getParam('fee');
        $orderId = (int) $this->getRequest()->getParam('orderId');

        if ($baseShippingFee < 0) {
            $baseShippingFee = 0;
        }

        try {
        	$order = $this->orderRepository->get($orderId);

            $taxPercent = $this->helperData->caculateTaxShippingFee($order);
            $shippingFee = $baseShippingFee + ($baseShippingFee * ($taxPercent / 100));
            $shippingFee = number_format($shippingFee, 2);

            $order->setBaseBssShippingFee($baseShippingFee);
            $order->setBssShippingFee($shippingFee);
            $order->save();

            $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            $invoice->setBaseBssShippingFee($baseShippingFee);
            $invoice->setBssShippingFee($shippingFee);

            $shippingFee = $this->pricing->currency(number_format($shippingFee, 2), true, false);


            $this->registry->register('current_invoice', $invoice);
            $invoiceRawCommentText = $invoiceData['comment_text'];
            $invoice->setCommentText($invoiceRawCommentText);

            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Invoices'));

            $response = [
                'error' => false,
                'message' => __('%1 fee have been added.', $shippingFee),
                'html' => $resultPage->getLayout()->getBlock('order_items')->setControllerName('order_invoice')->toHtml()
            ];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Cannot add fee shipping.')];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}