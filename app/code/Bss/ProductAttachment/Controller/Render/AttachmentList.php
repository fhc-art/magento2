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
 * @package    Bss_ProductAttachment
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductAttachment\Controller\Render;

use Magento\Framework\Controller\Result\JsonFactory;

class AttachmentList extends \Magento\Framework\App\Action\Action
{
    /**
     * Product Factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Helper
     *
     * @var \Bss\ProductAttachment\Helper\Data
     */
    protected $_helper;

    /**
     * Repository
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * Result Raw Factory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * Layout Factory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Bss\ProductAttachment\Helper\Data $helper
     * @param JsonFactory $resultJsonFactory
     * @internal param \Bss\ProductAttachment\Model\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Bss\ProductAttachment\Helper\Data $helper,
        JsonFactory $resultJsonFactory

    ) {
        $this->resultRawFactory = $resultRawFactory; 
        $this->layoutFactory = $layoutFactory;
        $this->_productFactory = $productFactory;
        $this->_assetRepo = $assetRepo;
        $this->_helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $productId = $this->getRequest()->getPost('product_id');
            $customerGroupId = $this->getRequest()->getPost('customer_groupId');
            $storeId = $this->getRequest()->getPost('store_id');

            $product = $this->_productFactory->create()->load($productId);

            $listAttachment = $product->getData('bss_productattachment');
            $listAttachment = explode(",", $listAttachment);
            $listAttachment = array_filter($listAttachment);

            $attachments = [];
            if ( isset( $listAttachment ) && !empty($listAttachment) ) {
                foreach ($listAttachment as $key => $value) {
                    $attachments[] = $this->_helper->getDataAttachmentById($value);
                }
            } else {
                $attachments = null;
            }
            
            $attachments = $this->_helper->sortAttachment($attachments);

            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $this->layoutFactory->create();

            $block = $layout->createBlock(\Bss\ProductAttachment\Block\Attachment\Ajax::class);
            $block->setAttachments($attachments);
            $block->setStoreId($storeId);
            $block->setCustomerGroupId($customerGroupId);

            $block->setTemplate('Bss_ProductAttachment::ajax/attachments.phtml');

            $resultJson->setData(['content' => $block->toHtml()]);
            return $resultJson;

        } else {
            $this->_redirect('no-route');
            return;
        }
    }

}
