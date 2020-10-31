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
 * @package    Bss_ShippingFee
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ShippingFee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Model\RegionFactory;

/**
 * Class Data
 *
 * @package Bss\ShippingFee\Helper
 */
class Data extends AbstractHelper
{
    const ENABLE = 'shipping_fee/general/active';

    const CONFIG_TITLE = 'shipping_fee/general/title';

    const CONFIG_STATE = 'shipping_fee/general/state';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    public $adminhtmlData;

     /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $adminhtmlData,
        RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->adminhtmlData = $adminhtmlData;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Is module enabled
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get title config shipping fee
     *
     * @param null|int $storeId
     * @return string
     */
    public function getTitle($storeId = null)
    {
        $title = $this->scopeConfig->getValue(
            self::CONFIG_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (!$title) {
            $title = __("Shipping Fee");
        }
        return $title;
    }

    /**
     * Get submit fee button url
     *
     * @return string
     */
    public function getSubmitFeeButtonUrl()
    {
        return $this->adminhtmlData->getUrl('shippingfee/ajax/index');
    }

    /**
     * Get State
     *
     * @param null|int $storeId
     * @return string
     */
    public function getState($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_STATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Caculate Tax PercentShippingFee
     *
     * @return float
     */
    public function caculateTaxShippingFee($order)
    {
        $taxPercent = 0;

        $regionId = $order->getShippingAddress()->getRegionId();
        $stateConfig = $this->getState();
        if ($stateConfig && $regionId) {
            $region = $this->regionFactory->create()->load($regionId);
            $regionCode = $region->getCode();
            $state = explode(',', $stateConfig);
            if (in_array($regionCode, $state)) {
                $items = $order->getItems();
                foreach ($items as $item) {
                    $taxItemPercent = $item->getTaxPercent();
                    if ($taxItemPercent > 0 && $taxItemPercent > $taxPercent) {
                        $taxPercent = $taxItemPercent;
                    }
                }
            }
        }
        return $taxPercent;
    }
}
