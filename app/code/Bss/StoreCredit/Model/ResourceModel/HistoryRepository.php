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

namespace Bss\StoreCredit\Model\ResourceModel;

use Bss\StoreCredit\Api\HistoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Bss\StoreCredit\Model\HistoryFactory;

/**
 * Class HistoryRepository
 * @package Bss\StoreCredit\Model\ResourceModel
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var \Bss\StoreCredit\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var array
     */
    private $historyRegistryById = [];

    /**
     * Constructor
     *
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        HistoryFactory $historyFactory
    ) {
        $this->historyFactory = $historyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($historyId)
    {
        if (isset($this->historyRegistryById[$historyId])) {
            return $this->historyRegistryById[$historyId];
        }

        $history = $this->historyFactory->create()->load($historyId);
        if (!$history->getId()) {
            // history does not exist
            throw new NoSuchEntityException(__('History doesn\'t exist'));
        } else {
            $this->historyRegistryById[$historyId] = $history;
        }
        return $history;
    }
}
