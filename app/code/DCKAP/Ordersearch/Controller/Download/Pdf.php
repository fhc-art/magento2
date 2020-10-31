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
use Magento\Framework\App\Filesystem\DirectoryList;

class Pdf extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \DCKAP\Ordersearch\Model\Ordersearch
     */
    protected $ordersDownloadModel; 

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;   

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    protected $pdf;
    

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
     * @param \DCKAP\Ordersearch\Model\ResourceModel\Ordersdownload $ordersDownloadModel,
     * @param \DCKAP\Ordersearch\Model\Pdf $pdfData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,        
        \DCKAP\Ordersearch\Model\ResourceModel\Ordersdownload $ordersDownloadModel,
        \DCKAP\Ordersearch\Model\Pdf $pdfData
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->ordersDownloadModel = $ordersDownloadModel;
        $this->pdf = $pdfData;
        $this->fileFactory = $fileFactory;            
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Customer order Search
     *
     * @return \DCKAP\Ordersearch\View\Result\Page
     */
    public function execute()
    {   
        $dataObject = $this->getRequest()->getPost();
            
        if($dataObject){
                          
            $result = $this->ordersDownloadModel->getOrders($dataObject);
            $pdfData = $this->pdf->getPdf($result);

            return $this->fileFactory->create(
                'orderaspdf.pdf',
                $pdfData->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }            
    }
}
