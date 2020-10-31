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
 * @package   Bss_AdminShippingMethod
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomShippingMethod\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\State;
use Bss\CustomShippingMethod\Model\ResourceModel\StoreView;

/**
 * Class Data
 *
 * @package Bss\CustomShippingMethod\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StoreView
     */
    protected $storeView;

    /**
     * @var \Bss\CustomShippingMethod\Model\ResourceModel\CustomMethod\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator
     */
    private $itemPriceCalculator;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form $form
     * @param \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator
     * @param \Bss\CustomShippingMethod\Model\ResourceModel\CustomMethod\CollectionFactory $collectionFactory
     * @param State $state
     * @param StoreView $storeView
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form $form,
        \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,
        \Bss\CustomShippingMethod\Model\ResourceModel\CustomMethod\CollectionFactory $collectionFactory,
        State $state,
        StoreView $storeView
    ) {
        parent::__construct($context);
        $this->state = $state;
        $this->storeManager = $form;
        $this->storeView = $storeView;
        $this->collectionFactory = $collectionFactory;
        $this->itemPriceCalculator = $itemPriceCalculator;
    }

    /**
     * Get value Active.
     *
     * @param int $storeId
     * @return mixed
     */
    public function getActive($storeId)
    {
        return $this->scopeConfig->getValue(
            'carriers/customshippingmethod/active',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Store ID Admin
     *
     * @return \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form|\Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * Get State.
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get Store View.
     *
     * @return StoreView
     */
    public function getStoreView()
    {
        return $this->storeView;
    }

    /**
     * Get Collection Method.
     *
     * @return \Bss\CustomShippingMethod\Model\ResourceModel\CustomMethod\CollectionFactory
     */
    public function getCollectionMethod()
    {
        return $this->collectionFactory;
    }

    /**
     * ItemPriceCalculator
     *
     * @return \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator
     */
    public function itemPriceCalculator()
    {
        return $this->itemPriceCalculator;
    }
}
