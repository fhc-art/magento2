<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Model\Config\Source;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * save Image
 */
class Saveimage extends \Magento\Config\Model\Config\Backend\Image
{
    const UPLOAD_DIR = 'Mageants'; // Folder save image
    protected $_mediaDirectory;
    
	/**
	 * get Upload directory for Image
	 *
	 * @return $this
	 */ 
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['icon'];
    }
}
