<?php

namespace Bss\CustomizeCreditLimit\Block\CreditLimit;

/**
 * Sales order fields filter block
 */
class FilterPaid extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'filter_paid.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    )
    {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }


    /**
     * @return array
     */
    public function getFilterList()
    {
        $selectFiledList = [
            'increment_id' => 'Order #',
            'invoice' => 'Invoice #',
            'purchase_order' => "Purchase Order #",
            'sku' => 'Item #',
            'created_at' => 'Date Paid #'
        ];
        return $selectFiledList;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('customizecredit/index/paid');
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->create()->getCustomer()->getId();
    }

}