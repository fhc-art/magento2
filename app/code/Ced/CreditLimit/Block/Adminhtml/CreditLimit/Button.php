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
namespace Ced\CreditLimit\Block\Adminhtml\CreditLimit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class kButton
 */
class Button implements ButtonProviderInterface
{
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Ced\CreditLimit\Helper\Data $helper
	) {
		$this->urlBuilder = $context->getUrlBuilder();
		$this->helper = $helper;
	}
    /**
     * @return array
     */
	
    public function getButtonData()
    {
    	$disabled = false;
    	if($this->helper->getAssignType()=='group')
    		$disabled = true;
    	
        return [
            'label' => __('Add Credit Limit'),
            'on_click' => sprintf("location.href = '%s';", $this->getNewUrl()),
            'class' => 'primary',
            'sort_order' => 10,
            'disabled'=>$disabled
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getNewUrl()
    {
        return $this->urlBuilder->getUrl('*/*/new');
    }
}