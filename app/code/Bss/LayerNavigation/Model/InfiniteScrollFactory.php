<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Model factory
 */
namespace Bss\LayerNavigation\Model;

class InfiniteScrollFactory
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create()
    {
        if (!class_exists(\Bss\InfiniteScroll\Block\InfiniteScroll::class)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Class doesn\'t not exist \Bss\InfiniteScroll\Block\InfiniteScroll')
            );
        }
        $model = $this->_objectManager->create(\Bss\InfiniteScroll\Block\InfiniteScroll::class);
        return $model;
    }
}
