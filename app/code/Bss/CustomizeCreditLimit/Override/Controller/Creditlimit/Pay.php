<?php
namespace Bss\CustomizeCreditLimit\Override\Controller\Creditlimit;
use Magento\Framework\Controller\ResultFactory;
class Pay extends \Ced\CreditLimit\Controller\Creditlimit\Pay
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $resultConfig = $this->getConfig('b2bextension/credit_limit/enable');
        if (!$resultConfig){
            return $this->_redirect('customer/account');
        }

        if (!$this->_custmerSesion->isLoggedIn()){
            return $this->_redirect ('customer/account/login');
        }
        if ($this->getRequest()->getParam('amount') == 0) {
            $this->messageManager->addNoticeMessage(__('Please select at least one order to pay !'));
            return $this->_redirect ('creditlimit/creditlimit');
        }

        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if($data){
            try{
                $productobj =  $this->productRepository->get(\Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU);
                $websiteIds = $productobj->getWebsiteIds();
                $currentWebsite = [$this->getCurrentWebsiteId()];
                $newWebsiteIds = array_unique(array_merge($websiteIds,$currentWebsite));
                $params = ['product'=>$productobj->getId(),'qty'=>1];
                $productobj->setPrice($this->getRequest()->getParam('amount'));
                $productobj->setWebsiteIds($newWebsiteIds);
                $productobj->save();
                $this->cart->truncate();
                $this->cart->setCreditLimitProduct(true);
                $this->cart->addProduct($productobj,$params);
                $this->cart->save();
                $price = $productobj->getPrice();
                $product = $this->cart->getQuote()->getItemByProduct($productobj);
                if ($this->getRequest()->getParam('string_invoice_credit_limit')) {
                    $product->setInvoiceCreditLimit($this->getRequest()->getParam('string_invoice_credit_limit'));
                }
                if ($this->getRequest()->getParam('string_order_credit_limit')) {
                    $product->setOrderCreditLimit($this->getRequest()->getParam('string_order_credit_limit'));
                }
                $product->setBaseTaxAmount(0);
                $product->setTaxPercent(0);
                $product->setTaxAmount(0);
                $product->setPriceInclTax($price);
                $product->setBasePriceInclTax($price);
                $product->setRowTotalInclTax($price);
                $product->setBaseRowTotalInclTax($price);
                $quote = $this->cart->getQuote();
                $this->cart->getQuote()->getShippingAddress()
                    ->setTaxAmount(0)
                    ->setBaseTaxAmount(0)
                    ->setGrandTotal($price)
                    ->setBaseGrandTotal($price);
                $quote->setGrandTotal($price);
                $quote->setBaseGrandTotal($price);
                $quote->save();
                return $this->_redirect('checkout');

            }catch(\Exception $e){
                $this->messageManager->addErrorMessage(__('Something Went Wrong'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        }

        $this->messageManager->addErrorMessage(__('Something Went Wrong'));
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
