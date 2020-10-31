<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CreditLimit
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CreditLimit\Block\Adminhtml\Pay\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ceditlimit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Balance'));
    }

    /**
     * @return \Magento\Backend\Block\Widget\Tabs
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
       $this->addTab(
            'credit_limit',
            [
                'label' => __('Manage Balance'),
                'title' => __('Manage Balance'),
                'content' => $this->getLayout()->createBlock('Ced\CreditLimit\Block\Adminhtml\Pay\Edit\Tab\Balance')->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
