<?php
/**
  * @author     DCKAP <extensions@dckap.com>
  * @package    DCKAP_Ordersearch
  * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace DCKAP\Ordersearch\Controller\Download;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Csv extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var DCKAP\Ordersearch\Model\Ordersearch
     */
    protected $ordersDownloadModel;    

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;
    protected $fileSystem;
    protected $directoryList;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \DCKAP\Ordersearch\Model\ResourceModel\Ordersdownload $ordersDownloadModel
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $dateTime
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \DCKAP\Ordersearch\Model\ResourceModel\Ordersdownload $ordersDownloadModel,
        \Magento\Framework\Stdlib\DateTime\Timezone $dateTime,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->ordersDownloadModel = $ordersDownloadModel;
        $this->customerSession = $customerSession;
        $this->coreRegistry = $coreRegistry;
        $this->dateTime = $dateTime;
        $this->priceHelper = $priceHelper;
        $this->fileSystem = $fileSystem;
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * Customer order Search
     *
     * @return \DCKAP\Ordersearch\View\Result\Page
     */
    public function execute()
    {   
        if (!($customerId = $this->customerSession->create()->getCustomerId())) {
            return false;
        } 

        $dataObject = $this->getRequest()->getPost();
            
            if($dataObject){
                              
                $result = $this->ordersDownloadModel->getOrders($dataObject);
                
                //$outputFile = $this->getFileName();

                $rootDirectory = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)."/Ordersearch";
                $this->fileSystem->createDirectory($rootDirectory);
                $outputFile = $rootDirectory."/".$this->getFileName();

                $handle = $this->fileSystem->fileOpen($outputFile, 'w');
                $this->fileSystem->filePutCsv($handle, $this->createHeading());
                foreach ($result as $_order) {
                    $createdDate = $this->dateTime->formatDate($_order->getCreatedAt());
                    $row = [
                        $_order->getRealOrderId(),
                        $createdDate,
                        $_order->getShippingAddress() ? $_order->getShippingAddress()->getName(): '&nbsp;',
                        $this->priceHelper->currency($_order->getGrandTotal(), true, false),
                        $_order->getStatus()
                    ];
                    $this->fileSystem->filePutCsv($handle, $row);
                }
                $this->downloadCsv($outputFile);
                
            }
        
        $resultPage = $this->resultPageFactory->create();            
        $resultPage->getConfig()->getTitle()->set(__('Order Search'));
        $resultPage->addHandle('ordersearch_index_result');
        return $resultPage; 
    }

    /**
     * download file name
     */
    protected function getFileName(){
        return __('Orders.csv');
    }

    /**
     * Create Csv file heading 
     */
    protected function createHeading(){

        return $heading = [
            __('Order #'),
            __('Date'),
            __('Ship To'),
            __('Order Total'),
            __('Status')
         ];
    }

    /**
     * Download the file content by csv
     * @param $file
     */
    protected function downloadCsv($file)
    {
         if (file_exists($file)) {
             //set appropriate headers
             header('Content-Description: File Transfer');
             header('Content-Type: application/csv');
             header('Content-Disposition: attachment; filename='.basename($file));
             header('Expires: 0');
             header('Cache-Control: must-revalidate');
             header('Pragma: public');
             header('Content-Length: ' . filesize($file));
             ob_clean();flush();
             readfile($file);
         }
    }

}
