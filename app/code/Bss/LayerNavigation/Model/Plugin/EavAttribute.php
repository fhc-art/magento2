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
namespace Bss\LayerNavigation\Model\Plugin;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Bss\LayerNavigation\Helper\Data as DataHelper;

class EavAttribute
{
    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $layerHelper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * EavAttribute constructor.
     * @param DataHelper $layerHelper
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        DataHelper $layerHelper,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->layerHelper = $layerHelper;
        $this->serialize = $serialize;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param Attribute $attribute
     */
    public function beforeSave(Attribute $attribute)
    {
        $version = $this->productMetadata->getVersion();
        $versionArray = explode(".", $version);

        $initialAdditionalData = [];
        $additionalData        = (string)$attribute->getData('additional_data');
        if (!empty($additionalData)) {
            if ($versionArray[1] < DataHelper::MAGENTO_VERSION_220) {
                $additionalData = $this->serialize->unserialize($additionalData);
            } else {
                $additionalData = json_decode($additionalData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Unable to unserialize value.');
                }
            }
            
            if (is_array($additionalData)) {
                $initialAdditionalData = $additionalData;
            }
        }

        $dataToAdd = [];
        foreach ($this->layerHelper->getLayerAdditionalFields() as $key) {
            $dataValue = $attribute->getData($key);
            if (null !== $dataValue) {
                $dataToAdd[$key] = $dataValue;
            }
        }
        $additionalData = array_merge($initialAdditionalData, $dataToAdd);
        if ($versionArray[1] < DataHelper::MAGENTO_VERSION_220) {
            $additionalDataEndcode = $this->serialize->serialize($additionalData);
        } else {
            $additionalDataEndcode = json_encode($additionalData);
            if (false === $additionalDataEndcode) {
                throw new \InvalidArgumentException('Unable to serialize value.');
            }
        }
        $attribute->setData('additional_data', $additionalDataEndcode);
    }
}
