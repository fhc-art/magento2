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
namespace Ced\CreditLimit\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class RemainingAmount
 * @package Ced\CreditLimit\Ui\Component\Listing\Columns
 */
class RemainingAmount extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

   /**
    * 
    * @param ContextInterface $context
    * @param UiComponentFactory $uiComponentFactory
    * @param \Ced\CreditLimit\Helper\Data $helper
    * @param array $components
    * @param array $data
    */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
       \Ced\CreditLimit\Helper\Data  $helper,  		
        array $components = [],
        array $data = []
    ) {
    	$this->helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
        	$storeId = $this->context->getFilterParam('store_id');
        	$fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
            	$item['remaining_amount'] = $this->helper->getFormattedPrice($item['remaining_amount']);
            }
        }
        return $dataSource;
    }
}
