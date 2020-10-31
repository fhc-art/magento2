<?php

namespace Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Bss\CustomizeCreditLimit\Model\CreditList', 'Bss\CustomizeCreditLimit\Model\ResourceModel\CreditList');
    }
}
