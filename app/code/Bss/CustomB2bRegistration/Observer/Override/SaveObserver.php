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
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomB2bRegistration\Observer\Override;

use Bss\B2bRegistration\Helper\Data;
use Bss\B2bRegistration\Helper\Email;
use Bss\B2bRegistration\Model\Config\Source\CustomerAttribute;
use Bss\CustomB2bRegistration\Plugin\Model\Config\Source\CustomerAttribute as PluginCustomerAttribute;

class SaveObserver extends \Bss\B2bRegistration\Observer\SaveObserver
{
    /**
     * @var \Bss\CustomB2bRegistration\Helper\Data
     */
    protected $data;

    /**
     * SaveObserver constructor.
     * @param Data $helper
     * @param Email $emailHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Bss\CustomB2bRegistration\Helper\Data $data
     */
    public function __construct(
        Data $helper,
        Email $emailHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Bss\CustomB2bRegistration\Helper\Data $data
    ) {
        $this->data = $data;
        parent::__construct($helper, $emailHelper, $customerRepositoryInterface);
    }

    /**
     * Send email to Customer when Admin Approval or Reject
     *
     * @param string $newStatus
     * @param string $oldStatus
     * @param array $customerEmail
     * @param string $customerName
     * @param int $storeId
     */
    protected function checkAndSend(&$customer, $newStatus, $oldStatus, $customerEmail, $customerName, $storeId)
    {
        $email = $this->helper->getCustomerEmailSender();
        $emailVar = [
            'varEmail'  => $customerEmail,
            'varName' => $customerName,
        ];
        if ($newStatus == CustomerAttribute::B2B_APPROVAL) {
            if ($oldStatus == CustomerAttribute::B2B_PENDING ||
                $oldStatus == CustomerAttribute::B2B_REJECT ||
                $oldStatus == CustomerAttribute::NORMAL_ACCOUNT ||
                $oldStatus == PluginCustomerAttribute::B2B_APPROVAL_PRIMARY
            ) {
                $emailTemplate = $this->helper->getCustomerApproveEmailTemplate($storeId);
                $this->emailHelper->sendEmail($email, $customerEmail, $emailTemplate, $storeId, $emailVar);
            }
        } elseif ($newStatus == CustomerAttribute::B2B_REJECT) {
            if ($oldStatus == CustomerAttribute::B2B_PENDING ||
                $oldStatus == CustomerAttribute::B2B_APPROVAL ||
                $oldStatus == CustomerAttribute::NORMAL_ACCOUNT ||
                $oldStatus == PluginCustomerAttribute::B2B_APPROVAL_PRIMARY
            ) {
                $emailTemplate = $this->helper->getCustomerRejectEmailTemplate($storeId);
                $this->emailHelper->sendEmail($email, $customerEmail, $emailTemplate, $storeId, $emailVar);
            }
        } elseif ($newStatus == PluginCustomerAttribute::B2B_APPROVAL_PRIMARY) {
            if ($oldStatus == CustomerAttribute::B2B_PENDING ||
                $oldStatus == CustomerAttribute::B2B_REJECT ||
                $oldStatus == CustomerAttribute::NORMAL_ACCOUNT ||
                $oldStatus == CustomerAttribute::B2B_APPROVAL
            ) {
                $emailTemplate = $this->data->getCustomerApprovePrimaryEmailTemplate($storeId);
                $this->emailHelper->sendEmail($email, $customerEmail, $emailTemplate, $storeId, $emailVar);
            }
        }
    }
}
