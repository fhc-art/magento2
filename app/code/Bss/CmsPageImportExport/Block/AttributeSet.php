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
namespace Bss\CmsPageImportExport\Block;

use Magento\Store\Model\System\Store as SystemStore;

class AttributeSet extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Cms\Ui\Component\Listing\Column\Cms\Options
     */
    protected $storeList;

    /**
     * @var \Magento\Framework\View\Element\Html\Select
     */
    protected $selectHtml;

    /**
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * AttributeSet constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Ui\Component\Listing\Column\Cms\Options $storeList
     * @param \Magento\Framework\View\Element\Html\Select $selectHtml
     * @param SystemStore $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Ui\Component\Listing\Column\Cms\Options $storeList,
        \Magento\Framework\View\Element\Html\Select $selectHtml,
        SystemStore $systemStore,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeList = $storeList;
        $this->selectHtml = $selectHtml;
        $this->systemStore = $systemStore;
    }

    /**
     * @return array
     */
    public function getCmsAttributes()
    {
        return [
            'page_layout' => __('Page Layout'),
            'meta_keywords' => __('Meta Keywords'),
            'meta_description' => __('Meta Description'),
            'identifier' => __('URL Key'),
            'content_heading' => __('Content Heading'),
            'content' => __('Content'),
            'creation_time' => __('Creation Time'),
            'update_time' => __('Modification Time'),
            'is_active' => __('Is Page Active'),
            'layout_update_xml' => __('Layout Update Content'),
            'custom_theme' => __('Custom Theme'),
            'custom_root_template' => __('Custom Template'),
            'custom_layout_update_xml' => __('Custom Layout Update Content'),
            'custom_theme_from' => __('Custom Theme Active From Date'),
            'custom_theme_to' => __('Custom Theme Active To Date'),
            'meta_title' => __('Meta Title')
        ];
    }

    /**
     * @return array
     */
    public function getStoreList()
    {
        return $this->storeList->toOptionArray();
    }
}
