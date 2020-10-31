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

/**
 * Visitor Observer
 */
class SaveOrderGird implements ObserverInterface
{
    /**
     * @var \Bss\CheckoutCustomField\Model\GridViewAttribute $gridViewAttribute
     */
    protected $gridViewAttributeFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Bss\CheckoutCustomField\Model\Attribute
     */
    protected $attribute;

    /**
     * SaveOrderGird constructor.
     * @param \Bss\CheckoutCustomField\Model\GridViewAttributeFactory $gridViewAttributeFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Bss\CheckoutCustomField\Model\Attribute $attribute
     * @param \Bss\CheckoutCustomField\Model\AttributeOption $attributeOption
     */
    public function __construct(
        \Bss\CheckoutCustomField\Model\GridViewAttributeFactory $gridViewAttributeFactory,
        \Magento\Framework\App\Request\Http $request,
        \Bss\CheckoutCustomField\Model\Attribute $attribute,
        \Bss\CheckoutCustomField\Model\AttributeOption $attributeOption
    ) {
        $this->gridViewAttributeFactory = $gridViewAttributeFactory;
        $this->request = $request;
        $this->attribute = $attribute;
        $this->attributeOption = $attributeOption;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $data = $this->request->getPost();
        if (isset($data['bss_customField'])) {
            $customAttr = $data['bss_customField'];
            $attrCodes = array_keys($customAttr);
            $collection = $this->attribute->getCollectionByCode($attrCodes);
            $data = [];
            $frontendType = ['select', 'boolean', 'dropdown'];
            foreach ($collection as $col) {
                if ($col->getShowGird() != 2) {
                    $value = $this->setValueData($customAttr, $col);
                    if ($col->getFrontendInput() == 'multiselect') {
                        $data[addslashes($col->getAttributeCode())]= addslashes(implode(",", $customAttr[$col->getAttributeCode()]));
                    } elseif (in_array($col->getFrontendInput(), $frontendType)) {
                        $data[addslashes($col->getAttributeCode())]= addslashes($customAttr[$col->getAttributeCode()]);
                    } else {
                        $data[addslashes($col->getAttributeCode())]= addslashes($value);
                    }
                }
            }
            if (!empty($data)) {
                $gridView = $this->gridViewAttributeFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('incrementId', $order->getIncrementId())
                    ->addFieldToFilter('store_id', $order->getStoreId())
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getLastItem();
                if ($gridView->getId()) {
                    $gridView->setData($data)->save();
                } else {
                    $data['incrementId'] = $order->getIncrementId();
                    $data['store_id'] = $order->getStoreId();
                    $this->gridViewAttributeFactory->create()->setData($data)->save();
                }
            }
        }
        return $this;
    }

    /**
     * @param $customAttr
     * @param $col
     * @return array|\Magento\Framework\Phrase|string
     */
    private function setValueData($customAttr, $col)
    {
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
        return $value;
    }
}
