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
namespace Bss\LayerNavigation\Plugin\AjaxProcess\Category;

class View
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $bssHelper;

    /**
     * View constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Bss\LayerNavigation\Helper\Data $bssHelper
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Bss\LayerNavigation\Helper\Data $bssHelper
    ) {
        $this->jsonHelper   = $jsonHelper;
        $this->bssHelper = $bssHelper;
    }

    /**
     * @param \Magento\Catalog\Controller\Category\View $action
     * @param $page
     * @return mixed
     */
    public function afterExecute(\Magento\Catalog\Controller\Category\View $action, $page)
    {
        $request = $action->getRequest();
        if ($request->getMethod() == 'POST' && $request->isAjax() && $this->bssHelper->isEnabled()) {
            $navigation = $page->getLayout()->getBlock('catalog.leftnav.bss');
            $products = $page->getLayout()->getBlock('category.products');
            $result = ['products' => $products->toHtml(), 'navigation' => $navigation->toHtml()];
            $action->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
        } else {
            return $page;
        }
    }
}
