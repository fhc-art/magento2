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
namespace Ced\CreditLimit\Block\Html\Link;


class Current extends \Magento\Framework\View\Element\Html\Link\Current
{
   
	/**
	 * (non-PHPdoc)
	 * @see \Magento\Framework\View\Element\Html\Link\Current::_toHtml()
	 */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        
        $creditLimitCheck = \Magento\Framework\App\ObjectManager::getInstance()->create('Ced\CreditLimit\Helper\Data')->checkLimitAssign();
        if(!$creditLimitCheck){
        	return false;
        }
        
        $cedhighlight = '';

        if ($this->getIsHighlighted()) {
            $cedhighlight = ' current';
        }
        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            $html .= '<strong>'
                . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()))
                . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $cedhighlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }
            $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }
            $html .= '</a></li>';
        }
        return $html;
    }
}
