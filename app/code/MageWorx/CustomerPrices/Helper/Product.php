<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerPrices\Helper;

use Magento\Framework\App\Helper\Context;
use \Magento\Framework\App\Helper\AbstractHelper;
use MageWorx\CustomerPrices\Helper\Calculate as HelperCalculate;
use MageWorx\CustomerPrices\Model\ResourceModel\CustomerPrices as ResourceCustomerPrices;

class Product extends AbstractHelper
{
    /**
     * @var ResourceCustomerPrices
     */
    protected $customerPricesResourceModel;

    /**
     * @var HelperCalculate
     */
    protected $helperCalculate;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param ResourceCustomerPrices $customerPricesResourceModel
     * @param Calculate $helperCalculate
     */
    public function __construct(
        Context $context,
        ResourceCustomerPrices $customerPricesResourceModel,
        HelperCalculate $helperCalculate
    ) {
        parent::__construct($context);

        $this->customerPricesResourceModel = $customerPricesResourceModel;
        $this->helperCalculate             = $helperCalculate;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     * @throws \Exception
     */
    public function getProductId($product)
    {
        return $product->getData($this->helperCalculate->getLinkField());
    }

    /**
     * Get product 'entity_id' or 'row_id', dependency magento community or enterprise
     *
     * @param int $productId
     * @return int
     * @throws \Exception
     */
    public function getLinkFieldIdByProductId($productId)
    {
        $linkFieldId = $this->customerPricesResourceModel->getLinkFieldId(
            $productId,
            $this->helperCalculate->getLinkField()
        );

        return $linkFieldId;
    }

    /**
     * Get array products 'entity_id' or 'row_id', dependency magento community or enterprise
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getLinkFieldIdsByProductIds($productIds)
    {
        $linkFieldIds = $this->customerPricesResourceModel->getLinkFieldIds(
            $productIds,
            $this->helperCalculate->getLinkField()
        );

        return $linkFieldIds;
    }

    /**
     * Retrieve the array. Format LinkField => EntityId:
     * 'row_id' => 'entity_id' for EE,
     * 'entity_id' => 'entity_id' for CE
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductIdsPairs($productIds)
    {
        $linkFieldIds = $this->customerPricesResourceModel->getProductIdsPairs(
            $productIds,
            $this->helperCalculate->getLinkField()
        );

        return $linkFieldIds;
    }

    /**
     * Return product entity id
     *
     * @param int $productId
     * @return int
     * @throws \Exception
     */
    public function getProductEntityId($productId)
    {
        $entityId = $this->customerPricesResourceModel->getEntityId($productId);
        if (isset($entityId)) {
            return $productId;
        }

        return $entityId;
    }

    /**
     * Return products entity_id
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductsEntityIds($productIds)
    {
        return $this->customerPricesResourceModel->getEntityIds($productIds);
    }

    /**
     * Check has tier price on product
     *
     * Magento has specifics
     * example product price = 100$
     *  tier 2 qty - price 70
     *  tier 3 qty - price 60
     *  if add 5 qty -> subtotal = 60 x 5 = 300.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $qty
     * @return bool
     */
    public function hasProductTierPrice($product, $qty)
    {
        if (empty($product->getData('tier_price'))) {
            return false;
        }

        return !empty($product->getTierPrice($qty));
    }
}