<?php
/**
  * @author     DCKAP <extensions@dckap.com>
  * @package    DCKAP_Ordersearch
  * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
namespace DCKAP\Ordersearch\Block\Order\Search;

/**
 * Sales order search product name block
 */
class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/search/product.phtml';

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
        $this->pageConfig->getTitle()->set(__('Products'));
    }
    /**
     * @return \DCKAP\Ordersearch\Model\Products
     */
    public function getProducts()
    {       
        return $this->coreRegistry->registry('suggestproduct');
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {        
        parent::_prepareLayout();
        if ($this->getProducts()) {           
            $this->getProducts()->load();
        }
        return $this;
    }    
    
}
