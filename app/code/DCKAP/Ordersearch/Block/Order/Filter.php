<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Block\Order;

/**
 * Sales order fields filter block
 */
class Filter extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/filter/fields.phtml';
    
    /**
     * @var \DCKAP\Ordersearch\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \DCKAP\Ordersearch\Model\Options\Config\Source
     */
    protected $optionValues;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \DCKAP\Ordersearch\Helper\Data $helper
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \DCKAP\Ordersearch\Model\Config\Source\Options $options
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        \DCKAP\Ordersearch\Helper\Data $helper, 
        \Magento\Customer\Model\SessionFactory $customerSession, 
        \DCKAP\Ordersearch\Model\Config\Source\Options $options, 
        array $data = []
        )
    {
        $this->helper         = $helper;
        $this->optionValues   = $options;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
        
    }
    
    /**
     * @return array
     */
    public function getFilterList()
    {
        $selectFiledList = [];
        
        $filedList   = explode(',', $this->helper->getOrderFilterList());
        $optionsList = $this->optionValues->toArray();
        
        foreach ($filedList as $fields) {
            if (array_key_exists($fields, $optionsList)) {
                $selectFiledList[$fields] = $optionsList[$fields];
            }
        }
        
        return $selectFiledList;
    }
    
    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('ordersearch/index/result');        
    }
    
    /**
     * @return string
     */
    public function getNameRequestUrl()
    {
        return $this->getUrl('ordersearch/index/productname');
    }
    
    /**
     * @return int
     */
    public function getNameSuggestionEnable()
    {
        return $this->helper->isNameSuggestionEnable();
    }
    
    /**
     * @return int
     */
    public function getNameMinLength()
    {
        return $this->helper->getMinQueryLength();        
    }
    
    /**
     * @return int
     */
    public function getNameMaxLength()
    {
        return $this->helper->getMaxQueryLength();        
    }
    
    /**
     * @return int
     */
    public function getErrorMesage()
    {
        return $this->helper->getSuggestLimitError();        
    }
    /**
     * @return int
     */
    public function isDateField()
    {
        return $this->helper->isDateFilterEnable();
    }
    
    /**
     * @return int
     */
    public function isDateRequired()
    {
        return $this->helper->isDateRequired();
    }
    
    /**
     * @return int
     */
    public function isDownloadEnable()
    {
        return $this->helper->isEnableOrderDownload();
    }
    /**
     * @return int
     */
    public function isCsvEnable()
    {
        return $this->helper->isEnableOrderDownloadCSV();
    }
    /**
     * @return int
     */
    public function isPdfEnable()
    {
        return $this->helper->isEnableOrderDownloadPDF();
    }
    
    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->create()->getCustomer()->getId();
    }
    
    /**
     * @return string
     */
    public function getDownloadPdfUrl()
    {
        return $this->getUrl('ordersearch/download/pdf');
    }
    
    /**
     * @return string
     */
    public function getDownloadCsvUrl()
    {
        return $this->getUrl('ordersearch/download/csv');
    }
    
}