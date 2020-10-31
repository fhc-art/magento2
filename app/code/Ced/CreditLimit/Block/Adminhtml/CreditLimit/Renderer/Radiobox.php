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

namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit\Renderer;

/**
 * Class Radiobox
 * @package Ced\CreditLimit\Block\Adminhtml\CreditLimit\Renderer
 */
class Radiobox extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$modeldata = $objectManager->create('Ced\CreditLimit\Model\CreditLimit')->load($this->getRequest()->getParam('id'));
		$html = '';
		if($modeldata->getId()){
			if($modeldata->getCustomerId() == $row->getId()){
			$html.= '<input type ="radio" value ="'.$modeldata->getCustomerId().'" checked = "checked" class ="radio" name ="customer_id"/>'; 
			}  else{
			$html.= '<input type ="radio" value ="'.$row->getId().'" class ="radio" name ="customer_id" disabled="disabled"/>';
		} 
		}
		else{
			$html.= '<input type ="radio" value ="'.$row->getId().'" class ="radio" name ="customer_id"/>';
		}
		
		return $html;
	}
}