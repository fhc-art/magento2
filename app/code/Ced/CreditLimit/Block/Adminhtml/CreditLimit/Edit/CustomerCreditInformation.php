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

namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit;
use Magento\Backend\Block\Widget\Context;

/**
 * Class CustomerCreditInformation
 * @package Ced\CreditLimit\Block\Adminhtml\CreditLimit\Edit
 */
class CustomerCreditInformation extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'Ced_CreditLimit::display_credit_limit.phtml';

    /**
     * CustomerCreditInformation constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->getAddButtonOptions();
        $this->setData('area','adminhtml');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Container
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\CreditLimit\Block\Adminhtml\CreditLimit\Grid\CreditlimitGrid', 'customer_credit_limit')
        );
        return parent::_prepareLayout();
    }

    /**
     *
     */
    protected function getAddButtonOptions()
    {
        $splitButtonOptions = [
            'label' => __('Add Credit Limit'),
            'class' => 'primary',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];
        $this->buttonList->add('add', $splitButtonOptions);
    }

    /***
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            '*/*/new'
        );
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
