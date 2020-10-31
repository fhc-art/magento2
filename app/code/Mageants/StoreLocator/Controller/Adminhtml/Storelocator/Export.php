<?php
/**
 * @category   Mageants CMSImportExport
 * @package    Mageants_CMSImportExport
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Export extends \Magento\Backend\App\Action
{
    /**
     * result page Factory
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Cms\Model\Block
     */
    protected $storeModel;

    protected $storeProduct;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Cms\Model\Block $cmsBlockModel
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Mageants\StoreLocator\Model\ManageStore $storeModel,
        \Mageants\StoreLocator\Model\StoreProduct $StoreProduct
    ) 
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->storeModel=$storeModel;
        $this->storeProduct=$StoreProduct;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }
    
    /**
     * Execute method for Attachment index action
     *
     * @return $resultPage
     */ 
    public function execute()
    {
        $id = $this->getRequest()->getParam('selected');

        try {
                $StoreArray=array();
                $fieldArray=array();
                $fileName="StoreLocator".date("Y_M_d_H_i_s").".csv";

                if(empty($id)){
                    $store=$this->storeModel->getCollection()
                        ->addFieldToSelect("store_id");
                    $stores=$store->getData();

                    foreach($stores as $key=>$val){
                        $tmpid[]=$val["store_id"];
                    }

                    $id=$tmpid;
                }

                foreach($id as $key=>$val){
                    $cmsBlock=$this->storeModel->load($val);
                    $productIds=$this->storeProduct->getCollection()
                                ->addFieldToSelect("product_id")
                                ->addFieldToFilter("store_id",$val);
                    $prdIds=array();
                    foreach ($productIds->getData() as $product => $id) {
                        $prdIds[]=$id['product_id'];
                    }
                    
                    $tmp=$cmsBlock->getData();
                    $tmp['product_id']=implode(",",$prdIds);
                    $StoreArray[]=$tmp;
                    $fieldArray=array_keys($tmp);
                }

                $f = fopen('php://memory', 'w');
                if(!empty($fieldArray)){
                    fputcsv($f, $fieldArray);
                }

                foreach ($StoreArray as $line) {
                    fputcsv($f, $line); 
                }
                // reset the file pointer to the start of the file
                fseek($f, 0);
                // tell the browser it's going to be a csv file
                header('Content-Type: application/csv');
                // tell the browser we want to save it instead of displaying it
                header('Content-Disposition: attachment; filename="'.$fileName.'";');
                // make php send the generated csv lines to the browser
                fpassthru($f);
                exit();

                $this->messageManager->addSuccess(
                    __('CMS Block Exported Successfully!')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        $this->_redirect('Storelocator/Storelocator/index/');
    }
}
