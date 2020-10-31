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
 * @package    Bss_CheckoutCustomField
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CheckoutCustomField\Model\Observer\Adminhtml;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Visitor Observer
 */
class SaveOrder implements ObserverInterface
{
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Bss\CheckoutCustomField\Model\Attribute
     */
    protected $attribute;

    /**
     * @var \Bss\CheckoutCustomField\Model\AttributeOption
     */
    protected $attributeOption;

    /**
     * SaveOrder constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param JsonHelper $jsonHelper
     * @param \Bss\CheckoutCustomField\Model\Attribute $attribute
     * @param \Bss\CheckoutCustomField\Model\AttributeOption $attributeOption
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        JsonHelper $jsonHelper,
        \Bss\CheckoutCustomField\Model\Attribute $attribute,
        \Bss\CheckoutCustomField\Model\AttributeOption $attributeOption
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->request = $request;
        $this->attribute = $attribute;
        $this->attributeOption = $attributeOption;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $data = $this->request->getPost();
        if (isset($data['bss_customField'])) {
            $customAttr = $data['bss_customField'];
            $attrCodes = array_keys($customAttr);
            $bssCustomfield = [];
            $collection = $this->attribute->getCollectionByCode($attrCodes);
            foreach ($collection as $col) {
                if (is_array($customAttr[$col->getAttributeCode()])) {
                    $value = [];
                    foreach ($customAttr[$col->getAttributeCode()] as $val) {
                        $value[] = $this->attributeOption->getLabel($col->getAttributeId(), $val);
                    }
                } elseif ($col->getFrontendInput() == 'select' || $col->getFrontendInput() == 'dropdown') {
                    $value = $this->attributeOption->getLabel($col->getAttributeId(), $customAttr[$col->getAttributeCode()]);
                } elseif ($col->getFrontendInput() == 'boolean') {
                    $value = $customAttr[$col->getAttributeCode()] ? __("Yes") : __("No");
                } else {
                    $value = $customAttr[$col->getAttributeCode()];
                }
                $bssCustomfield[$col->getAttributeCode()] = [
                    'show_gird' => $col->getShowGird(),
                    'show_in_order' => $col->getShowInOrder(),
                    'show_in_pdf' => $col->getShowInPdf(),
                    'show_in_email' => $col->getShowInEmail(),
                    'frontend_label' => $col->getBackendLabel(),
                    'value' => $value,
                    'val' => $customAttr[$col->getAttributeCode()],
                    'type' => $col->getFrontendInput()
                ];
            }
            $order->setBssCustomfield($this->jsonHelper->jsonEncode($bssCustomfield));
            $order->save();
        }
        return $this;
    }
}
