<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Model\ResourceModel;

use Amasty\CustomerLogin\Api\Data\LoggedInInterface;
use Amasty\CustomerLogin\Setup\Operation\CreateCustomerLoginLogTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LoggedIn extends AbstractDb
{
    public function _construct()
    {
        $this->_init(CreateCustomerLoginLogTable::TABLE_NAME, LoggedInInterface::LOGGEDIN_ID);
    }
}
