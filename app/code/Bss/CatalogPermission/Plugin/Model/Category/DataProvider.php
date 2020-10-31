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
 * @package    Bss_CatalogPermission
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CatalogPermission\Plugin\Model\Category;

use Magento\Framework\Module\Manager as ModuleManager;
use Bss\CatalogPermission\Model\Category\Attribute\Source\CustomSource;
use Bss\CatalogPermission\Helper\ModuleConfig;

/**
 * Class DataProvider
 *
 * @package Bss\CatalogPermission\Plugin\Model\Category
 */
class DataProvider
{
    /**
     * @var array
     * @since 101.0.0
     */
    protected $meta = [];

    /**
     * @var ModuleManager
     * @since 101.0.0
     */
    protected $moduleManager;

    /**
     * @var CustomSource
     */
    protected $customSource;

    /**
     * @var ModuleConfig
     */
    protected $bssModuleConfig;

    /**
     * DataProvider constructor.
     * @param ModuleManager $moduleManager
     * @param CustomSource $customSource
     * @param ModuleConfig $bssModuleConfig
     */
    public function __construct(
        ModuleManager $moduleManager,
        CustomSource $customSource,
        ModuleConfig $bssModuleConfig
    ) {
        $this->moduleManager = $moduleManager;
        $this->customSource = $customSource;
        $this->bssModuleConfig = $bssModuleConfig;
    }

    /**
     * Add Meta to Category Form
     *
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $meta
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPrepareMeta(
        \Magento\Catalog\Model\Category\DataProvider $subject,
        $meta
    ) {
        $meta = $this->addBssCustomerGroup($meta);
        return $meta;
    }

    /**
     * Bss Customer Group MetaData
     *
     * @param array $meta
     * @return array
     */
    private function addBssCustomerGroup($meta)
    {
        $this->meta = $meta;
        $this->meta['catalog_permission'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Catalog Permission'),
                        'collapsible' => true,
                        'sortOrder' => 6,
                        'componentType' => 'fieldset',
                    ]
                ]
            ],
            'children' => [
                'bss_customer_group' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => 'string',
                                'formElement' => 'multiselect',
                                'componentType' => 'field',
                                'options' => $this->getCustomerGroups(),
                                'label' => __('Customer Group'),
                                'scopeLabel' => __('[STORE VIEW]'),
                                'sortOrder' => 0
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->meta;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @return array
     */
    private function getCustomerGroups()
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }
        return $this->customSource->toOptionArray();
    }
}
