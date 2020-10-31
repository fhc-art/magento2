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
namespace Bss\CmsPageImportExport\Model\Import;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

/**
 * Class CmsPage
 *
 * @package Bss\CmsPageImportExport\Model\Import
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CmsPage extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const ENTITY_TYPE_CODE = 'cms_page';

    const VALIDATOR_MAIN = 'validator';

    const DEFAULT_OPTION_VALUE_SEPARATOR = ';';

    const ERROR_CODE_MISSING_COLUMNS = 'Missing Column(s): %s';

    const ERROR_INVALID_PAGE_TITLE= 'Invalid value in page title column';

    const ERROR_INVALID_URL_KEY = 'Invalid value in URL key column';

    const ERROR_LAYOUT_INVALID = 'Invalid layout value in %s column';

    const ERROR_TIME_INVALID = 'Invalid time value in %s column';

    /**
     * @var array
     */
    protected $validColumnNames = [
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
     * @var \Magento\Eav\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $cmsPageFactory;

    /**
     * @var CmsPage\Validator\PageConfig
     */
    protected $cmsPageValidator;

    /**
     * CmsPage constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param CmsPage\Validator\PageConfig $cmsPageValidator
     * @param \Magento\Cms\Model\PageFactory $cmsPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        CmsPage\Validator\PageConfig $cmsPageValidator,
        \Magento\Cms\Model\PageFactory $cmsPageFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->config = $config;
        $this->_connection = $resource;
        $this->_dataSourceModel = $importData;
        $this->productMetadata = $productMetadata;
        $this->cmsPageFactory = $cmsPageFactory;
        $this->cmsPageValidator = $cmsPageValidator;
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return static::ENTITY_TYPE_CODE;
    }

    /**
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE !== $this->getBehavior()) {
            if (!isset($rowData['title']) || $rowData['title'] === '') {
                $this->addRowError(self::ERROR_INVALID_PAGE_TITLE, $rowNum, 'title');
                return false;
            }
        }
        if (!isset($rowData['identifier']) || $rowData['identifier'] === '') {
            $this->addRowError(
                self::ERROR_INVALID_URL_KEY,
                $rowNum,
                'identifier',
                null,
                ProcessingError::ERROR_LEVEL_NOT_CRITICAL
            );
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteCmsPage();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceCmsPage();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveCmsPage();
        }

        return true;
    }

    /**
     * @return $this|Import\Entity\AbstractEntity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _saveValidatedBunches()
    {
        $source = $this->_getSource();
        $currentDataSize = 0;
        $bunchRows = [];
        $startNewBunch = false;
        $nextRowBackup = [];
        $maxDataSize = $this->_resourceHelper->getMaxDataSize();
        $bunchSize = $this->_importExportData->getBunchSize();

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunch($this->getEntityTypeCode(), $this->getBehavior(), $bunchRows);

                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen(serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($source->valid()) {
                try {
                    $rowData = $source->current();
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }

                $this->_processedRowsCount++;

                if ($this->validateRow($rowData, $source->key())) {
                    // add row to bunch for save
                    $rowData = $this->_prepareRowForDb($rowData);
                    $rowSize = strlen($this->jsonHelper->jsonEncode($rowData));

                    $isBunchSizeExceeded = $this->isBunchSizeExceeded($bunchSize, $bunchRows);

                    if ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded) {
                        $startNewBunch = true;
                        $nextRowBackup = [$source->key() => $rowData];
                    } else {
                        $bunchRows[$source->key()] = $rowData;
                        $currentDataSize += $rowSize;
                    }
                }
                $source->next();
            }
        }
        return $this;
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function saveCmsPage()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                if (isset($rowData['page_layout']) &&
                    !$this->cmsPageValidator->validateLayout($rowData['page_layout'])) {
                    $this->addRowError(
                        self::ERROR_LAYOUT_INVALID,
                        $rowNum,
                        'page_layout',
                        null,
                        ProcessingError::ERROR_LEVEL_NOT_CRITICAL
                    );
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                if (isset($rowData['custom_root_template']) &&
                    !$this->cmsPageValidator->validateLayout($rowData['custom_root_template'])) {
                    $this->addRowError(
                        self::ERROR_LAYOUT_INVALID,
                        $rowNum,
                        'custom_root_template',
                        null,
                        ProcessingError::ERROR_LEVEL_NOT_CRITICAL
                    );
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                if (isset($rowData['custom_theme_from'])) {
                    if (!$this->cmsPageValidator->validateDate($rowData['custom_theme_from'])) {
                        $this->addRowError(
                            self::ERROR_TIME_INVALID,
                            $rowNum,
                            'custom_theme_from',
                            null,
                            ProcessingError::ERROR_LEVEL_NOT_CRITICAL
                        );
                        $this->getErrorAggregator()->addRowToSkip($rowNum);
                        continue;
                    } else {
                        $rowData['custom_theme_from'] =
                        $this->cmsPageValidator->dateFormat($rowData['custom_theme_from']);
                    }
                }

                if (isset($rowData['custom_theme_to'])) {
                    if (!$this->cmsPageValidator->validateDate($rowData['custom_theme_to'])) {
                        $this->addRowError(
                            self::ERROR_TIME_INVALID,
                            $rowNum,
                            'custom_theme_to',
                            null,
                            ProcessingError::ERROR_LEVEL_NOT_CRITICAL
                        );
                        $this->getErrorAggregator()->addRowToSkip($rowNum);
                        continue;
                    } else {
                        $rowData['custom_theme_to'] =
                        $this->cmsPageValidator->dateFormat($rowData['custom_theme_to']);
                    }
                }
                $this->processData($rowData);
            }
        }
    }

    /**
     * @param $rowData
     * @throws \Exception
     */
    protected function processData($rowData)
    {
        $page = $this->cmsPageFactory->create()->getCollection()
        ->getItemByColumnValue('identifier', $rowData['identifier']);

        $value[0] = '0';
        $value[1] = '1';
        
        if (!in_array($rowData['is_active'], $value)) {
            $rowData['is_active'] = 0;
        }

        if (isset($rowData['creation_time'])) {
            if (!$this->cmsPageValidator->validateDate($rowData['creation_time'])) {
                $rowData['creation_time'] = null;
            } else {
                $rowData['creation_time'] = $this->cmsPageValidator->dateFormat($rowData['creation_time']);
            }
        }

        $rowData = $this->getUpdateTime($rowData);

        if (isset($rowData['store_id'])) {
            $storeList = explode($this->getMultipleValueSeparator(), $rowData['store_id']);
            if ($rowData['store_id'] == '' || !$this->cmsPageValidator->validateStoreId($storeList)) {
                $rowData['store_id'] = $this->cmsPageValidator->getDefaultStoreId();
            } else {
                $rowData['store_id'] = $storeList;
            }
        } else {
            $rowData['store_id'] = $this->cmsPageValidator->getDefaultStoreId();
        }

        if (empty($page)) {
            $cmsPageModel = $this->cmsPageFactory->create();
            $cmsPageModel->addData($rowData);
            $cmsPageModel->save();
        } else {
            $cmsPageModel = $this->cmsPageFactory->create();
            $cmsPageModel->load($page->getPageId());
            $cmsPageModel->addData($rowData);
            $cmsPageModel->save();
        }
    }

    /**
     * Replace product attributes
     *
     * @return void
     */
    protected function replaceCmsPage()
    {
        $this->deleteForReplace();
        $this->saveCmsPage();
    }

    /**
     * Delete product attributes
     *
     * @return $this|bool
     */
    protected function deleteCmsPage()
    {
        $listIdentifier = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowIdentifier = $rowData['identifier'];
                    $listIdentifier[$rowNum] = $rowIdentifier;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated() &&
                    version_compare($this->productMetadata->getVersion(), '2.2.0', '<')) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        $this->cmsPageFactory->create()->getCollection()
                ->addFieldToFilter('identifier', ['in' => $listIdentifier])->walk('delete');
        return $this;
    }

    /**
     * Delete product attributes for replace
     *
     * @return $this|bool
     */
    protected function deleteForReplace()
    {
        $this->cmsPageFactory->create()->getCollection()->walk('delete');
        return $this;
    }

    /**
     * Get multiple value separator for import
     *
     * @return string
     */
    public function getMultipleValueSeparator()
    {
        if (!empty($this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR])) {
            return $this->_parameters[Import::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR];
        }
        return Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR;
    }

    /**
     * @param $rowData
     * @return mixed
     */
    protected function getUpdateTime($rowData)
    {
        if (isset($rowData['update_time'])) {
            if (!$this->cmsPageValidator->validateDate($rowData['update_time'])) {
                $rowData['update_time'] = null;
            } else {
                $rowData['update_time'] = $this->cmsPageValidator->dateFormat($rowData['update_time']);
            }
        }
        return $rowData;
    }

    /**
     * @param $bunchSize
     * @param $bunchRows
     * @return bool
     */
    protected function isBunchSizeExceeded($bunchSize, $bunchRows)
    {
        $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;
        return $isBunchSizeExceeded;
    }
}
