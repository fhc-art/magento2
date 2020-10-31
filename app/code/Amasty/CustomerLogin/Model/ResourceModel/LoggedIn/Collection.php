<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Model\ResourceModel\LoggedIn;

use Amasty\CustomerLogin\Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model\LoggedIn::class, Model\ResourceModel\LoggedIn::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
        parent::_construct();
    }
}
