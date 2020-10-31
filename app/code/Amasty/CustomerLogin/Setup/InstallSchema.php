<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\CustomerLogin\Setup\Operation;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateCustomerLoginLogTable
     */
    private $customerLoginLogTable;

    public function __construct(
        Operation\CreateCustomerLoginLogTable $customerLoginLogTable
    ) {

        $this->customerLoginLogTable = $customerLoginLogTable;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->customerLoginLogTable->execute($setup);
        $setup->endSetup();
    }
}
