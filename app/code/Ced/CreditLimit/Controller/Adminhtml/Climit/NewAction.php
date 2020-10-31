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

namespace Ced\CreditLimit\Controller\Adminhtml\Climit;
/**
 * Class NewAction
 * @package Ced\CreditLimit\Controller\Adminhtml\Climit
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * New promo quote action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
