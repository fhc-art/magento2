<?php
/**
 * Order
 *
 * @copyright Copyright © 2019 Firebear Studio. All rights reserved.
 * @author    fbeardev@gmail.com
 */

namespace Firebear\CustomImportExport5179\Model\Export;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Order
 * @package Firebear\CustomImportExport5179\Model\Export
 */
class Order extends \Firebear\ImportExport\Model\Export\Order
{
    /**
     * Export one item
     *
     * @param AbstractModel $item
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function exportItem($item)
    {
        $exportData = $this->_getExportData($item);
        $exportData = $this->filterExportData($exportData);
        foreach ($this->filters as $table => $isValid) {
            if (false === $isValid) {
                return;
            }
        }
        foreach ($exportData as $row) {
            $this->getWriter()->writeRow($row);
        }
        $this->_processedEntitiesCount++;
    }

    /**
     * @param $exportData
     * @return array
     * @throws \ReflectionException
     */
    public function filterExportData($exportData)
    {
        $newData = [];
        foreach ($exportData as $key => $exportDatum) {
            foreach ($exportDatum as $itemKey => $itemValue) {
                if (is_object($itemValue)) {
                    $objectdata = [];
                    $className = get_class($itemValue);
                    foreach (get_class_methods($itemValue) as $methodName) {
                        if ($methodName === 'getOrder') {
                            continue;
                        }
                        $r = new \ReflectionMethod($className, $methodName);
                        if (empty($r->getParameters())) {
                            if (strpos($methodName, 'get') !== false) {
                                if (!is_object($itemValue->{$methodName}())) {
                                    $objectdata[$className][$methodName] = $itemValue->{$methodName}();
                                }
                            }
                        }
                    }
                    $itemValue = json_encode($objectdata);
                }
                $newData[$key][$itemKey] = $itemValue;
            }
        }
        return $newData;
    }
}
