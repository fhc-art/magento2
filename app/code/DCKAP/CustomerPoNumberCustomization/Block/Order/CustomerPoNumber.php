<?php
/**
  * @author     DCKAP <extensions@dckap.com>
  * @package    DCKAP_Ordersearch
  * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace DCKAP\CustomerPoNumberCustomization\Block\Order;

/**
 * Sales order search result block
 */
class CustomerPoNumber extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    // protected $_template = 'order/result/result.phtml';

    /**
     *@var \Magento\Sales\Model\ResourceModel\Order\Collection 
     */
    protected $orders;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context    
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,  
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) { 
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        // $this->pageConfig->getTitle()->set(__('My Orders'));
    }


    public function getPoNumber($bssField)
    {
        $bssCustomField = json_decode($bssField,true);
        if(!empty($bssCustomField) && array_key_exists('purchase_order' , $bssCustomField))
        {
            $poValue = $bssCustomField['purchase_order']['value'];
            return $poValue;
        }
        return null;
    }
}
