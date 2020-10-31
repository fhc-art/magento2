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
namespace Ced\CreditLimit\Model\ResourceModel\CreditLimit;

/**
 * Class Collection
 * @package Ced\CreditLimit\Model\ResourceModel\CreditLimit
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
	protected $_idFieldName = 'id';

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Ced\CreditLimit\Model\CreditLimit', 'Ced\CreditLimit\Model\ResourceModel\CreditLimit');
    }
}
