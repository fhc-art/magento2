<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * save store Action
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Adapter Factory
     *
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;
    
    /**
     * Upload Factory
     *
     * @var Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;
    
    /**
     * File System
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    
    /**
     * Field Id
     *
     * @var $string='image'
     */
    protected $fileId = 'image';
    
    /**
     * Js Helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * Managet Store
     *
     * @var Mageants\StoreLocator\Model\ManageStore
     */
    protected $_manageStore;

    
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Backend\Helper\Js
     * @param \Magento\Framework\Image\AdapterFactory
     * @param Magento\MediaStorage\Model\File\UploaderFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList
     * @param \Magento\Framework\Filesystem
     * @param Mageants\StoreLocator\Model\ManageStore
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Directory\Model\Region $regionDataCollection,
        \Mageants\StoreLocator\Model\ManageStore $manageStore,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        $this->_jsHelper = $jsHelper;
        $this->adapterFactory = $adapterFactory;
        $this->_regionDataCollection = $regionDataCollection;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->directory_list = $directory_list;
        $this->_manageStore = $manageStore;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context);
    }

    
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_StoreLocator::save');
    }

    /**
     * perform execute method for save Action
     *
     * @return $resultRedirect
     */
    public function execute()
    {
        $data =$this->getRequest()->getPostValue();
        $region = $this->_regionDataCollection->loadByName($data['region'], trim($data['country']));
        $regionId = 0;
        $regionCode = 0;
        if ($region->getData()) {
            if ($region->getRegionId()) {
                $regionId = $region->getRegionId();
                $regionCode = $region->getCode();
            } else {
                $region = $this->_regionFactory->create()->getCollection()->addFieldToFilter('name', $data['region'])->getData();
                $regionId = $region['region_id'];
                $regionCode = $region['code'];
            }
        }
        $data['region_id'] = $regionId;
        $data['code'] = $regionCode;
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model=$this->_manageStore;
            if (isset($data['image']['delete'])) {
                $data['image']="";
            } else {
                if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                    $imagename=$this->uploadFile();
                    if ($imagename!="") {
                        $data['image']="Mageants".$imagename;
                    }
                } else {
                    if (isset($data['image'])) {
                        $data['image'] = $data['image']['value'];
                    }
                }
            }
            if (isset($data['icon']['delete'])) {
                $data['icon']="";
            } else {
                if (isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != '') {
                    $imagename=$this->uploadIcon();
                    if ($imagename!="") {
                        $data['icon']="Mageants/Icon".$imagename;
                    }
                } else {
                    if (isset($data['icon'])) {
                        $data['icon'] = $data['icon']['value'];
                    }
                }
            }

            if (isset($data['storeId'])) {
                if (in_array('0', $data['storeId'])) {
                    $data['storeId'] = '0';
                } else {
                    $data['storeId'] = implode(",", $data['storeId']);
                }
            }
            if (isset($data['mon_otime'])) {
                $data['mon_otime']=implode(",", $data['mon_otime']);
            }
            if (isset($data['mon_bstime'])) {
                $data['mon_bstime']=implode(",", $data['mon_bstime']);
            }
            if (isset($data['mon_betime'])) {
                $data['mon_betime']=implode(",", $data['mon_betime']);
            }
            if (isset($data['mon_ctime'])) {
                $data['mon_ctime']=implode(",", $data['mon_ctime']);
            }

            if (isset($data['tue_otime'])) {
                $data['tue_otime']=implode(",", $data['tue_otime']);
            }
            if (isset($data['tue_bstime'])) {
                $data['tue_bstime']=implode(",", $data['tue_bstime']);
            }
            if (isset($data['tue_betime'])) {
                $data['tue_betime']=implode(",", $data['tue_betime']);
            }
            if (isset($data['tue_ctime'])) {
                $data['tue_ctime']=implode(",", $data['tue_ctime']);
            }

            if (isset($data['wed_otime'])) {
                $data['wed_otime']=implode(",", $data['wed_otime']);
            }
            if (isset($data['wed_bstime'])) {
                $data['wed_bstime']=implode(",", $data['wed_bstime']);
            }
            if (isset($data['wed_betime'])) {
                $data['wed_betime']=implode(",", $data['wed_betime']);
            }
            if (isset($data['wed_ctime'])) {
                $data['wed_ctime']=implode(",", $data['wed_ctime']);
            }

            if (isset($data['thu_otime'])) {
                $data['thu_otime']=implode(",", $data['thu_otime']);
            }
            if (isset($data['thu_bstime'])) {
                $data['thu_bstime']=implode(",", $data['thu_bstime']);
            }
            if (isset($data['thu_betime'])) {
                $data['thu_betime']=implode(",", $data['thu_betime']);
            }
            if (isset($data['thu_ctime'])) {
                $data['thu_ctime']=implode(",", $data['thu_ctime']);
            }

            if (isset($data['fri_otime'])) {
                $data['fri_otime']=implode(",", $data['fri_otime']);
            }
            if (isset($data['fri_bstime'])) {
                $data['fri_bstime']=implode(",", $data['fri_bstime']);
            }
            if (isset($data['fri_betime'])) {
                $data['fri_betime']=implode(",", $data['fri_betime']);
            }
            if (isset($data['fri_ctime'])) {
                $data['fri_ctime']=implode(",", $data['fri_ctime']);
            }

            if (isset($data['sat_otime'])) {
                $data['sat_otime']=implode(",", $data['sat_otime']);
            }
            if (isset($data['sat_bstime'])) {
                $data['sat_bstime']=implode(",", $data['sat_bstime']);
            }
            if (isset($data['sat_betime'])) {
                $data['sat_betime']=implode(",", $data['sat_betime']);
            }
            if (isset($data['sat_ctime'])) {
                $data['sat_ctime']=implode(",", $data['sat_ctime']);
            }

            if (isset($data['sun_otime'])) {
                $data['sun_otime']=implode(",", $data['sun_otime']);
            }
            if (isset($data['sun_bstime'])) {
                $data['sun_bstime']=implode(",", $data['sun_bstime']);
            }
            if (isset($data['sun_betime'])) {
                $data['sun_betime']=implode(",", $data['sun_betime']);
            }
            if (isset($data['sun_ctime'])) {
                $data['sun_ctime']=implode(",", $data['sun_ctime']);
            }

            $model->setData($data);
            if (isset($data['id'])) {
                $model->setId($data['id']);
            }
            
            try {
                $model->save();
                /*$this->saveSchedule($model, $data);*/
                $this->saveProducts($model, $data);
                $this->messageManager->addSuccess(__('You saved this Record.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit/', ['store_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __($e->getMessage()));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['store_id' => $this->getRequest()->getParam('store_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    /**
     * upload imege file
     *
     * @return $void
     */
    public function uploadFile()
    {
        $destinationPath = $this->getDestinationPath();
        
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'image'])
                ->setAllowCreateFolders(true);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result=$uploader->save($destinationPath);
                
            return $result['file'];
            if (!$uploader->save($destinationPath)) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }
    
    /**
     * upload imege file
     *
     * @return $void
     */
    public function uploadIcon()
    {
        $destinationPath = $this->getIconPath();
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'icon'])
                ->setAllowCreateFolders(true);
            $uploader->setAllowedExtensions(array('icon'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result=$uploader->save($destinationPath);
                
            return $result['file'];
            if (!$uploader->save($destinationPath)) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }

    /**
     * Get Destination Path
     *
     * @return $directory_list
     */
    public function getDestinationPath()
    {
        return $this->directory_list->getPath('media')."/Mageants/";
    }
    
    /**
     * Get Destination Path
     *
     * @return $directory_list
     */
    public function getIconPath()
    {
        return $this->directory_list->getPath('media')."/Mageants/Icon";
    }

    /**
     * save product for store
     *
     * @return $void
     */
    public function saveProducts($model, $post)
    {
        if (isset($post['products'])) {
            $productIds = $this->_jsHelper->decodeGridSerializedInput($post['products']);
            try {
                $oldProducts = (array) $model->getProducts($model);
                $newProducts = (array) $productIds;
                
                $this->_resources = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $this->_resources->getConnection();

                $table = $this->_resources->getTableName(\Mageants\StoreLocator\Model\ResourceModel\ManageStore::TBL_ATT_PRODUCT);
                $insert = array_diff($newProducts, $oldProducts);
                $delete = array_diff($oldProducts, $newProducts);
                
                if ($delete) {
                    $where = ['store_id = ?' => (int)$model->getId(), 'product_id IN (?)' => $delete];
                    $connection->delete($table, $where);
                }

                if ($insert) {
                    $data = [];
                    foreach ($insert as $product_id) {
                        $data[] = ['store_id' => (int)$model->getId(), 'product_id' => (int)$product_id];
                    }
                    $connection->insertMultiple($table, $data);
                }
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Store.'));
            }
        }
    }

    /**
     * save product for store
     *
     * @return $void
     */
    /*public function saveSchedule($model, $data)
    {
        $modelSchedule=$this->_storeSchedule;
        $data['store_id']=$model->getId();

        $data['mon_otime']=implode(",",$data['mon_otime']);
        $data['mon_bstime']=implode(",",$data['mon_bstime']);
        $data['mon_betime']=implode(",",$data['mon_betime']);
        $data['mon_ctime']=implode(",",$data['mon_ctime']);

        $data['tue_otime']=implode(",",$data['tue_otime']);
        $data['tue_bstime']=implode(",",$data['tue_bstime']);
        $data['tue_betime']=implode(",",$data['tue_betime']);
        $data['tue_ctime']=implode(",",$data['tue_ctime']);

        $data['wed_otime']=implode(",",$data['wed_otime']);
        $data['wed_bstime']=implode(",",$data['wed_bstime']);
        $data['wed_betime']=implode(",",$data['wed_betime']);
        $data['wed_ctime']=implode(",",$data['wed_ctime']);

        $data['thu_otime']=implode(",",$data['thu_otime']);
        $data['thu_bstime']=implode(",",$data['thu_bstime']);
        $data['thu_betime']=implode(",",$data['thu_betime']);
        $data['thu_ctime']=implode(",",$data['thu_ctime']);

        $data['fri_otime']=implode(",",$data['fri_otime']);
        $data['fri_bstime']=implode(",",$data['fri_bstime']);
        $data['fri_betime']=implode(",",$data['fri_betime']);
        $data['fri_ctime']=implode(",",$data['fri_ctime']);

        $data['sat_otime']=implode(",",$data['sat_otime']);
        $data['sat_bstime']=implode(",",$data['sat_bstime']);
        $data['sat_betime']=implode(",",$data['sat_betime']);
        $data['sat_ctime']=implode(",",$data['sat_ctime']);

        $data['sun_otime']=implode(",",$data['sun_otime']);
        $data['sun_bstime']=implode(",",$data['sun_bstime']);
        $data['sun_betime']=implode(",",$data['sun_betime']);
        $data['sun_ctime']=implode(",",$data['sun_ctime']);

        $modelSchedule->setData($data);
        $modelSchedule->save();
    }   */
}
