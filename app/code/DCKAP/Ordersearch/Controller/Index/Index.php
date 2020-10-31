<?php
/**
  * @author     DCKAP <extensions@dckap.com>
  * @package    DCKAP_Ordersearch
  * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
namespace DCKAP\Ordersearch\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory     
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory                
    ) {
        $this->resultPageFactory = $resultPageFactory;                
        parent::__construct($context);
    }

    /**
     * Customer order Search
     *
     * @return \DCKAP\Ordersearch\View\Result\Page
     */
    public function execute()
    {       
        $resultPage = $this->resultPageFactory->create();            
        $resultPage->getConfig()->getTitle()->set(__('Order Search'));

        return $resultPage;    
    }
}
