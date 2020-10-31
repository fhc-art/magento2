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
 * @category  BSS
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Bss\LayerNavigation\Model\UpdateRating $updateRating
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->updateRating = $updateRating;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'rating');

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'rating',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => __('Rating'),
                'input' => 'select',
                'class' => '',
                'source' => \Bss\LayerNavigation\Model\Config\Source\Rating::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'           => true,
                'required'          => false,
                'user_defined'      => false,
                'default'           => null,
                'searchable'        => false,
                'filterable'        => true,
                'filterable_in_search' => true,
                'comparable'        => false,
                'visible_on_front'  => false,
                'unique'            => false,
                'used_in_product_listing' => true,
                'position'          => 9999999
            ]
        );
        $setup->endSetup();
        $this->updateRating->apply();
    }
}
