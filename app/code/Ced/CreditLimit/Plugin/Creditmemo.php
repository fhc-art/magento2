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
 * @category    Ced
 * @package     Ced_CreditLimit
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CreditLimit\Plugin;

class Creditmemo
{
   /**
    * 
    * @param  $subject
    * @param  $result
    * @return boolean|unknown
    */
	
    public function afterCanCreditmemo($subject, $result)
    {
        if($subject->getCreditdueOrder())
        	return false;
        
        return $result;
    }
}