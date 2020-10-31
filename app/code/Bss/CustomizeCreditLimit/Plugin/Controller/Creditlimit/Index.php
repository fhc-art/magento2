<?php

namespace Bss\CustomizeCreditLimit\Plugin\Controller\Creditlimit;

class Index
{
    /**
     * Index constructor.
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Customer\Model\Session $session
    ) {
        $this->custmerSesion = $session;
    }
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function afterExecute(\Ced\CreditLimit\Controller\Creditlimit\Index $subject, $result)
    {
        if($this->custmerSesion->isLoggedIn()) {
            $result->getConfig()->getTitle()->prepend(__('Invoices/Payments'));
        }
        return $result;
    }
}
