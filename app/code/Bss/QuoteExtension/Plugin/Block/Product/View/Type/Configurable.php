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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\QuoteExtension\Plugin\Block\Product\View\Type;

/**
 * Class Configurable
 *
 * @package Bss\QuoteExtension\Plugin\Block\Product\View\Type
 */
class Configurable
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonHelper;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $configurableData;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Bss\QuoteExtension\Helper\Data
     */
    private $helper;

    /**
     * Configurable constructor.
     * @param \Magento\Framework\Json\EncoderInterface $jsonHelper
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableData
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Bss\QuoteExtension\Helper\Data $helper
     */
    public function __construct(
        \Bss\QuoteExtension\Helper\Json $jsonHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableData,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Bss\QuoteExtension\Helper\Data $helper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->configurableData = $configurableData;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
    }

    /**
     * Plugin after jsonConfig product data
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetJsonConfig($subject, $result)
    {
        if ($this->helper->isEnable() &&
            in_array($this->helper->getCustomerGroupId(), $this->helper->getApplyForCustomers())) {
            $childProduct = $this->getAllData($subject->getProduct()->getEntityId());
            $config = $this->jsonHelper->unserialize($result);
            $config["quoteExtension"] = $childProduct;
            return $this->jsonHelper->serialize($config);
        }
        return $result;
    }

    /**
     * Get All Data From product id
     *
     * @param int $productEntityId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAllData($productEntityId)
    {
        $result = [];
        $map_r = [];
        $parentProduct = $this->configurableData->getChildrenIds($productEntityId);
        $product = $this->productRepository->getById($productEntityId);
        $parentAttribute = $this->configurableData->getConfigurableAttributes($product);
        $result['entity'] = $product->getId();
        foreach ($parentAttribute as $attrKey => $attrValue) {
            foreach ($product->getAttributes()[$attrValue->getProductAttribute()->getAttributeCode()]
                         ->getOptions() as $tvalue) {
                $map_r[$attrValue->getAttributeId()][$tvalue->getLabel()] = $tvalue->getValue();
            }
        }

        foreach ($parentProduct[0] as $simpleProduct) {
            $childProduct = [];
            $childProduct['entity'] = $simpleProduct;
            $child = $this->productRepository->getById($childProduct['entity']);
            $childProduct['enable'] = $this->helper->isActiveRequest4Quote($child);
            $key = '';
            foreach ($parentAttribute as $attrKey => $attrValue) {
                $attrLabel = $attrValue->getProductAttribute()->getAttributeCode();
                if (is_array($child->getAttributes()) && !empty($child->getAttributes())) {
                    $childRow = $child->getAttributes()[$attrLabel]->getFrontend()->getValue($child);
                    $key .= $map_r[$attrValue->getAttributeId()][$childRow] . '_';
                }
            }
            $result['child'][$key] = $childProduct;
        }
        return $result;
    }
}
