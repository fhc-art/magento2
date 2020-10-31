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

namespace Bss\StoreCredit\Block\Adminhtml\Report;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Toolbar
 * @package Bss\StoreCredit\Block\Adminhtml\Report
 */
class Toolbar extends Template
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DateTime $date
     * @param array $data
     */
    public function __construct(
        Context $context,
        DateTime $date,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->date = $date;
    }

    /**
     * Return date periods
     *
     * @return array
     */
    public function getPeriods()
    {
        return ['day' => __('Day'), 'month' => __('Month'), 'year' => __('Year')];
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('storecredit/report/ajax');
    }

    /**
     * @return string
     */
    public function getFromDefault()
    {
        return $this->date->date('m/d/Y', $this->getToDefault() . '-15 days');
    }

    /**
     * @return string
     */
    public function getToDefault()
    {
        return $this->date->date('m/d/Y');
    }
}
