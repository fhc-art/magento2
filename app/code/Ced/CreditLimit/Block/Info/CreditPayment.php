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
  * @license      https://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CreditLimit\Block\Info;
/**
 * Class CreditPayment
 * @package Ced\CreditLimit\Block\Info
 */
class CreditPayment extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Ced_CreditLimit::info/creditlimit.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Ced_CreditLimit::info/pdf/creditlimit.phtml');
        return $this->toHtml();
    }
}
