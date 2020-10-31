<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


declare(strict_types=1);

namespace Amasty\CustomerLogin\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeSchemaTo110
     */
    private $upgradeSchemaTo110;

    public function __construct(
        Operation\UpgradeSchemaTo110 $upgradeSchemaTo110
    ) {
        $this->upgradeSchemaTo110 = $upgradeSchemaTo110;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->upgradeSchemaTo110->execute($setup);
        }

        $setup->endSetup();
    }
}
