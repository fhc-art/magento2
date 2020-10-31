<?php

namespace Bss\CustomizeCmindMultiAccount\Plugin;

class MethodPlugin
{
    /**
     *
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Customer\Model\Session $session
     * @param \Ced\CreditLimit\Helper\Data $helper
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $session,
        \Ced\CreditLimit\Helper\Data $helper,
        \Cminds\MultiUserAccounts\Helper\View $view
    ){
        $this->helper = $helper;
        $this->cart = $cart;
        $this->session = $session;
        $this->view = $view;
    }
    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Payment\Model\MethodInterface[]
     * @api
     */
    public function afterGetAvailableMethods($subject, $result)
    {

        if($this->session->isLoggedIn()){
            $customerId = $this->session->getCustomerId();
            if ($this->view->isSubaccountLoggedIn() !== false) {
                $sub = $this->session->getSubaccountData();
                $customerId = $sub->getParentCustomerId();
            }

            $creditdata = $this->helper->getCustomerCreditLimit($customerId);
            $isOfflineHide = $this->helper->getConfigValue('b2bextension/credit_limit/hide_offline');
            $flag = false;
            $discountTotal = 0;

            foreach ($this->cart->getQuote()->getAllItems() as $item){
                if($item->getSku()==\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU){
                    $flag = true;
                    break;
                }
                $discountTotal += $item->getDiscountAmount();
            }

            $total = $this->cart->getQuote()->getBaseGrandTotal();

            $paymentamount = $total-$discountTotal;
            if($creditdata->getRemainingAmount() < $paymentamount || $flag){
                foreach($result as $key => $payment){

                    if($payment->getCode() == 'paybycredit'){
                        unset($result[$key]);
                    }

                    if($flag && $isOfflineHide){
                        if($payment->IsOffline()){
                            unset($result[$key]);
                        }
                    }
                }
            }
            return $result;
        }else{
            foreach($result as $key => $payment){
                if($payment->getCode() == 'paybycredit'){
                    unset($result[$key]);
                }
            }
            return $result;
        }

    }
}