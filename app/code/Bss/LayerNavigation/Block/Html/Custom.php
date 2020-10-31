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
namespace Bss\LayerNavigation\Block\Html;

use Magento\Framework\View\Element\Template;

class Custom extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    public $dataHelper;

    /**
     * Custom constructor.
     * @param Template\Context $context
     * @param \Bss\LayerNavigation\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\LayerNavigation\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return \Bss\LayerNavigation\Helper\Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }
}
