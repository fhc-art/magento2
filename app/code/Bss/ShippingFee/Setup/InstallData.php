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

namespace Bss\ShippingFee\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Sales\Model\Order;

/**
 * Class InstallData
 *
 * @package Bss\ShippingFee\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * Init
     *
     * @param SalesSetupFactory $salesSetupFactor
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute(
            Order::ENTITY,
            'bss_shipping_fee',
            ['type' => Table::TYPE_DECIMAL]
        );

        $salesSetup->addAttribute(
            Order::ENTITY,
            'base_bss_shipping_fee',
            ['type' => Table::TYPE_DECIMAL]
        );

        $salesSetup->addAttribute(
            'invoice',
            'bss_shipping_fee',
            ['type' => Table::TYPE_DECIMAL]
        );

        $salesSetup->addAttribute(
            'invoice',
            'base_bss_shipping_fee',
            ['type' => Table::TYPE_DECIMAL]
        );

        $setup->endSetup();
    }
}