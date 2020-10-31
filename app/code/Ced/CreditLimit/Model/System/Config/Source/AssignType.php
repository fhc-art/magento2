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
namespace Ced\CreditLimit\Model\System\Config\Source;
class AssignType
{
    /**
     *
     */
	const CUSTOMER = 'customer' ;
    /**
     *
     */
	const GROUP = 'group' ;

    /**
     * @return array
     */
	public function toOptionArray()
	{
		return [
				[
				'value'=>self::CUSTOMER,
				'label'=>__('Customer Wise')
				],
		      
				[
					'value'=>self::GROUP,
					'label'=>__('Customer Group Wise')
				],					
			];			
	}
}
