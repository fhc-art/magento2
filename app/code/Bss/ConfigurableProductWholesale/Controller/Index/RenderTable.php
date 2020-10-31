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
 * @package   Bss_ConfigurableProductWholesale
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\ConfigurableProductWholesale\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Bss\ConfigurableProductWholesale\Model\ConfigurableData;

/**
 * Class RenderTable
 *
 * @package Bss\ConfigurableProductWholesale\Controller\Index
 */
class RenderTable extends \Magento\Framework\App\Action\Action
{
    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var Configurable
     */
    private $typeConfigurable;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Configurable
     */
    private $configurableProductType;

    /**
     * @var AttributeFactory
     */
    private $eavModel;

    /**
     * @var ConfigurableData
     */
    private $configurableData;

    /**
     * @param Action\Context $context
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     * @param ProductRepository $productRepository
     * @param Configurable $configurableProductType
     * @param AttributeFactory $eavModel
     * @param ConfigurableData $configurableData
     */
    public function __construct(
        Action\Context $context,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        ProductRepository $productRepository,
        Configurable $configurableProductType,
        AttributeFactory $eavModel,
        ConfigurableData $configurableData
    ) {
        parent::__construct($context);
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->productRepository = $productRepository;
        $this->configurableProductType = $configurableProductType;
        $this->eavModel = $eavModel;
        $this->configurableData = $configurableData;
    }

    /**
     * Load product data
     *
     * @return mixed
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('options');
        $options = $this->jsonDecoder->decode($data);
        $productId = $options['productId'];
        $product = $this->productRepository->getById($productId);
        $childProducts = $this->configurableProductType->getUsedProductCollection($product)
            ->addAttributeToSelect('*');
        foreach ($options['option'] as $option) {
            $attr = explode('_', $option);
            $attributeCode = $this->loadAttributeCode($attr);
            $childProducts->addAttributeToFilter($attributeCode, $attr[1]);
        }

        $mergedIds = $childProducts->getAllIds();
        $jsonChildInfo = $this->configurableData->getJsonChildInfo($product, $mergedIds);
        return $this->getResponse()->setBody(
            $jsonChildInfo
        );
    }

    /**
     * Load atribute code
     *
     * @param array $attr
     * @return string
     */
    private function loadAttributeCode($attr)
    {
        return $this->eavModel->create()->load($attr[0])->getAttributeCode();
    }
}
