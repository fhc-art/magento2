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
 * @package    Bss_CustomizeCreditLimit
 * @author     Extension Team
 * @copyright  Copyright (c) 2019-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomizeCreditLimit\Block\CreditLimit;

class Index extends \Ced\CreditLimit\Block\CreditLimit\Index
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $creditlimitcontext,
        \Magento\Customer\Model\Session $creditlimitcustomerSession,
        \Ced\CreditLimit\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory,
        \Ced\CreditLimit\Model\ResourceModel\CreditOrder\CollectionFactory $creditOrderFactory,
        \Ced\CreditLimit\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList\CollectionFactory $creditListCollectionFact,
        \Magento\Sales\Model\Order $order,
        array $data = []
    ) {
        $this->creditListCollectionFact = $creditListCollectionFact;
        $this->order = $order;
        parent::__construct(
            $creditlimitcontext,
            $creditlimitcustomerSession,
            $transactionFactory,
            $creditOrderFactory,
            $helper,
            $orderCollectionFactory,
            $customerFactory,
            $data
        );
    }

    public function getOrders(){

        $customerId = $this->_customerSession->getCustomerId();
        $orderCollection = $this->creditOrderFactory->create();

        $orderCollection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        $orderCollection->addFieldToFilter('check_paid_order', ['null' => true]);

        $order_ids =  $orderCollection->getColumnValues('order_id');

        $salesOrderCollection = $this->orderCollection->create()
            ->addFieldToFilter('entity_id',['in'=>$order_ids])
            ->addFieldToFilter('state', ['nin' => ['canceled','closed']]);
        $salesOrderCollection->setOrder('entity_id','DESC');
        //get values of current page
        $page = $this->getRequest()->getParam('p', 1);
        //get values of current limit
        $pageSize = $this->getRequest()->getParam('limit', 10);
        $salesOrderCollection->setPageSize($pageSize);
        $salesOrderCollection->setCurPage($page);
        return $salesOrderCollection;

    }

    public function getPoNumber($bssField)
    {
        $bssCustomField = json_decode($bssField,true);
        if(!empty($bssCustomField) && array_key_exists('purchase_order' , $bssCustomField))
        {
            $poValue = $bssCustomField['purchase_order']['value'];
            return $poValue;
        }
        return null;
    }

    public function getPayOrders()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $orderCollection = $this->creditListCollectionFact->create();
        $orderCollection->addFieldToFilter('customer_id', ['eq' => $customerId]);
        return $orderCollection;
    }
    /**
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->order->formatPrice($price);
    }
    
}
