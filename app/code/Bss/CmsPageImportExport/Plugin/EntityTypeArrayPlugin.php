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
namespace Bss\CmsPageImportExport\Plugin;

class EntityTypeArrayPlugin extends \Magento\ImportExport\Model\Source\Import\Entity
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * EntityTypeArrayPlugin constructor.
     * @param \Magento\ImportExport\Model\Import\ConfigInterface $importConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\ImportExport\Model\Import\ConfigInterface $importConfig,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        parent::__construct($importConfig);
    }

    /**
     * @param $subject
     * @param $proceed
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundToOptionArray($subject, $proceed)
    {
        $bssOptions = [];
        $bssOptions[] = ['label' => __('-- Please Select --'), 'value' => ''];
        $options = [];
        $result = $proceed();
        foreach ($result as $entityConfig) {
            if (strpos($entityConfig['value'], 'cms_page')!==false) {
                $bssOptions[] = $entityConfig;
            } else {
                $options[] = $entityConfig;
            }
        }

        if ($this->request->getRouteName() === 'cmspageimportexport') {
            return $bssOptions;
        } else {
            return $options;
        }
    }
}
