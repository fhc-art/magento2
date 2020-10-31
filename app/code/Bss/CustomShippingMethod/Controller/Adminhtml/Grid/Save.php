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
 * @package    Bss_CustomShippingMethod
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomShippingMethod\Controller\Adminhtml\Grid;

/**
 * Class Save
 *
 * @package Bss\CustomShippingMethod\Controller\Adminhtml\Grid
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Bss\CustomShippingMethod\Model\CustomMethodFactory
     */
    protected $customMethodFactory;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Bss\CustomShippingMethod\Model\CustomMethodFactory $customMethodFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bss\CustomShippingMethod\Model\CustomMethodFactory $customMethodFactory
    ) {
        parent::__construct($context);
        $this->customMethodFactory = $customMethodFactory;
    }

    /**
     * Get Data.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('customshippingmethod/grid/index');
            return;
        }
        $data = $this->checkNull($data);

        $data["specific_countries"] = $this->checkSpecificCountries($data);

        if ($this->checkAmount($data)) {
            return;
        }

        try {
            $dataCustomMethod = $this->customMethodFactory->create();
            $dataCustomMethod->setData($data);
            $dataCustomMethod->setStoreId($data['store_id']);
            $dataCustomMethod->save();
            $this->messageManager->addSuccessMessage(__('Method has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/addcustommethod', ['id' => $dataCustomMethod->getId(), '_current' => true]);
        }
        $this->_redirect('customshippingmethod/grid/index');
    }

    /**
     * Check Amount
     *
     * @param array $data
     * @return bool
     */
    protected function checkAmount($data)
    {
        if (isset($data['id'])) {
            if (isset($data['minimum_order_amount']) && isset($data['maximum_order_amount'])
                && $data['minimum_order_amount'] >= $data['maximum_order_amount']) {
                $this->_redirect('*/*/addcustommethod', ['id' => $data['id'], '_current' => true]);
                $this->messageManager->addErrorMessage(__("Min Order Amount < Max Order Amount"));
                return true;
            }
            return false;
        }
        if (isset($data['minimum_order_amount']) && isset($data['maximum_order_amount'])
                && $data['minimum_order_amount'] >= $data['maximum_order_amount']) {
            $this->_redirect('*/*/addcustommethod');
            $this->messageManager->addErrorMessage(__("Min Order Amount  < Max Order Amount"));
            return true;
        }
        return false;
    }

    /**
     * Check SpecificCountries
     *
     * @param array $data
     * @return string|null
     */
    private function checkSpecificCountries($data)
    {
        if ($data['applicable_countries'] == 0) {
            return null;
        }
        return implode(",", $data['specific_countries']);
    }

    /**
     * Check Null.
     *
     * @param array $data
     * @return mixed
     */
    private function checkNull($data)
    {
        foreach ($data as $key => $val) {
            if ($val == "") {
                $data[$key] = null;
            }
        }
        return $data;
    }

    /**
     *  Function isAllowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_CustomShippingMethod::edit')
            && $this->_authorization->isAllowed('Bss_CustomShippingMethod::view');
    }
}
