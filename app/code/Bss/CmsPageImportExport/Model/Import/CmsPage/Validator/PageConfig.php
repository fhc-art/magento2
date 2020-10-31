<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CmsPageImportExport
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CmsPageImportExport\Model\Import\CmsPage\Validator;

use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;

class PageConfig
{
    /**
     * @var BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $context
     */
    protected $storeManager ;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * PageConfig constructor.
     * @param BuilderInterface $pageLayoutBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        BuilderInterface $pageLayoutBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * @param string $data
     * @return bool
     */
    public function validateLayout($data)
    {
        $layoutList = $this->pageLayoutBuilder->getPageLayoutsConfig()->getOptions();
        return array_key_exists($data, $layoutList);
    }

    /**
     * @param string $data
     * @return int
     */
    public function validateDate($data)
    {
        return $this->dateTime->gmtTimestamp($data);
    }

    /**
     * @param string $data
     * @return int
     */
    public function dateFormat($data)
    {
        return $this->dateTime->date(null, $data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validateStoreId($data)
    {
        $storeList = array_keys($this->storeManager->getStores(true));
        foreach ($data as $value) {
            if (!is_numeric($value) || (int)$value != $value) {
                return false;
            }
            if (array_search($value, $storeList) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return int
     */
    public function getDefaultStoreId()
    {
        return $this->storeManager->getDefaultStoreView()->getId();
    }
}
