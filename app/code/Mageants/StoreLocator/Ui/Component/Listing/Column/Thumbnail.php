<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Ui\Component\Listing\Column;
use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Locator store thumbnail Image
 */
class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const IMAGE_WIDTH = '70%'; // Thumbnail Image Width
    const IMAGE_HEIGHT = '60'; // Thumbnail Image Height
    const IMAGE_STYLE = 'display: block;margin: auto;'; // Thumbnail Image Style
	
    /**
	 * Current Store Manager
	 *
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
    
	/**
	 * @param \Magento\Backend\Block\Template\Context 
	 * @param \Magento\Framework\View\Element\UiComponentFactory
	 * @param \Magento\Framework\Filesystem
	 * @param \Magento\Store\Model\StoreManagerInterface
	 * @param array $components
	 * @param array $data
	 */
	public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) 
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
    }

	/**
	 * prepare Data source
	 *
	 * @param array $dataSource
	 * @return $dataSource
	 */ 
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $this->_prepareItem($item);
            }
        }
        return $dataSource;
    }

    /**
	 * prepare Item 
	 *
     * @param array $row
     * @return null|string
     */
     protected function _prepareItem(array & $item)
     {
        $width = $this->hasData('width') ? $this->getWidth() : self::IMAGE_WIDTH;
        $height = $this->hasData('height') ? $this->getHeight() : self::IMAGE_HEIGHT;
        $style = $this->hasData('style') ? $this->getStyle() : self::IMAGE_STYLE;
        if (isset($item[$this->getData('name')])) {
            if ($item[$this->getData('name')]) {
                $srcImage = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $item[$this->getData('name')];
                $item[$this->getData('name')] = sprintf(
                    '<img src="%s"  width="%s" height="%s" style="%s" />',
                    $srcImage,
                    $width,
                    $height,
                    $style
                );
            } else {
                $item[$this->getData('name')] = '';
            }
        }
        return $item;
     }
    
	/**
	 * prepare Alt 
	 *
     * @param $row
     * @return null|string
     */
	protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
 }
