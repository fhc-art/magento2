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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Controller\Adminhtml\Report;

use Bss\StoreCredit\Controller\Adminhtml\StoreCredit;

/**
 * Class Ajax
 * @package Bss\StoreCredit\Controller\Adminhtml\Report
 */
class Ajax extends StoreCredit
{
    /**
     * @return void
     */
    public function execute()
    {
        $dateStart = $this->getRequest()->getParam('from');
        $dateEnd = $this->getRequest()->getParam('to');
        $dimension = $this->getRequest()->getParam('dimension');
        $result = [];
        $result['status'] = false;
        try {
            if ($dateStart && $dateEnd) {
                $data = $this->historyFactory->create()->loadReportData($dateStart, $dateEnd, $dimension);
                $result['data'] = $this->jsonHelper->jsonEncode($data);
                $result['status'] = true;
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }
}
