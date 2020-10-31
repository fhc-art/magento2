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
namespace Bss\CmsPageImportExport\Model\ResourceModel\Export;

use Magento\Framework\App\ResourceConnection;

class CmsPage
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $magentoVersion;

    /**
     * @var array
     */
    protected $defaultColumns = [
        'title',
        'page_layout',
        'meta_keywords',
        'meta_description',
        'identifier',
        'content_heading',
        'content',
        'creation_time',
        'update_time',
        'is_active',
        'layout_update_xml',
        'custom_theme',
        'custom_root_template',
        'custom_layout_update_xml',
        'custom_theme_from',
        'custom_theme_to',
        'meta_title'
    ];

    /**
     * CmsPage constructor.
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\App\ProductMetadataInterface $magentoVersion
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\ProductMetadataInterface $magentoVersion,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->timezone = $timezone;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->pageFactory = $pageFactory;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @param array $requestData
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCmsPages($requestData)
    {
        $collection = $this->pageFactory->create()->getCollection();
        if (isset($requestData['export_filter'])) {
            $columnToFilter = [];
            foreach ($this->defaultColumns as $code) {
                if (!array_key_exists($code, $requestData['export_filter'])) {
                    $columnToFilter[] = $code;
                }
            }
            $collection->addFieldToSelect($columnToFilter);
        }

        if (isset($requestData['store_filter'])) {
            $collection->addStoreFilter($requestData['store_filter'], false);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('Filter store not selected'));
        }

        $result = $collection->toArray();
        if ($result['totalRecords'] > 0) {
            return $result['items'];
        }
        return [];
    }

    /**
     * @param $urlRewrites
     * @param $requestData
     * @return array
     */
    public function getExportData($urlRewrites, $requestData)
    {
        if (isset($requestData['export_filter'])) {
            $data[0] = [];
            foreach ($this->defaultColumns as $code) {
                if (!array_key_exists($code, $requestData['export_filter'])) {
                    $data[0][] = $code;
                }
            }
        } else {
            $data[0] = $this->defaultColumns;
        }

        if (!isset($requestData['exclude_store_id'])) {
            $data[0][] = 'store_id';
        }
        foreach ($urlRewrites as $urlRewrite) {
            $row = [];
            $urlRewrite = $this->getRewriteStoreId($requestData, $urlRewrite);
            foreach ($data[0] as $code) {
                $row[] = $urlRewrite[$code];
            }
            $data[] = $row;
        }
        return $data;
    }

    /**oad
     * @param string $dateTime
     * @return string
     */
    public function formatDate($dateTime)
    {
        $dateTimeAsTimeZone = $this->timezone
            ->date($dateTime)
            ->format('YmdHis');
        return $dateTimeAsTimeZone;
    }

    /**
     * @param $requestData
     * @param $urlRewrite
     * @return mixed
     */
    protected function getRewriteStoreId($requestData, $urlRewrite)
    {
        if (!isset($requestData['exclude_store_id']) && $this->magentoVersion->getEdition() === 'Enterprise') {
            $urlRewrite['store_id'] = $this->pageFactory->create()->load($urlRewrite['page_id'])->getStoreId();
        }
        if (isset($urlRewrite['store_id'])) {
            $urlRewrite['store_id'] = implode('|', $urlRewrite['store_id']);
        }
        return $urlRewrite;
    }
}
