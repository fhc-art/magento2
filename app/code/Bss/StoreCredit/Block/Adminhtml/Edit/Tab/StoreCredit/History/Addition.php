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
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Block\Adminhtml\Edit\Tab\StoreCredit\History;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Bss\StoreCredit\Model\History;
use Magento\Backend\Block\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Framework\DataObject;

/**
 * Class Addition
 * @package Bss\StoreCredit\Block\Adminhtml\Edit\Tab\StoreCredit\History
 */
class Addition extends AbstractRenderer
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        parent::__construct($context, $data);
    }

    /**
     * Renders a column
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = '<span>';
        $type = $row->getType();
        switch ($type) {
            case History::TYPE_UPDATE:
                $value .= $row->getCommentContent();
                break;
            case History::TYPE_USED_IN_ORDER:
                $order = $this->orderRepository->get($row->getOrderId());
                $url = $this->getUrl(
                    'sales/order/view',
                    ['order_id' => $row->getOrderId()]
                );
                $value .= '<a href="'. $url .'"">';
                $value .= __('Order # %1', $order->getIncrementId());
                $value .= '</a>';
                break;
            case History::TYPE_REFUND:
                $creditmemo = $this->creditmemoRepository->get($row->getCreditmemoId());
                $url = $this->getUrl(
                    'sales/creditmemo/view',
                    ['creditmemo_id' => $row->getCreditmemoId()]
                );
                $value .= '<a href="'. $url .'"">';
                $value .= __('Credit Memo # %1', $creditmemo->getIncrementId());
                $value .= '</a>';
                break;
            default:
                break;
        }

        $value .= '</span>';
        return $value;
    }
}
