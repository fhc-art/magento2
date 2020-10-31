<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Model;

use Amasty\CustomerLogin\Api\Data\LoggedInInterface;
use Magento\Framework\Model\AbstractModel;

class LoggedIn extends AbstractModel implements LoggedInInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\CustomerLogin\Model\ResourceModel\LoggedIn::class);
        $this->setIdFieldName(LoggedInInterface::LOGGEDIN_ID);
    }

    /**
     * @return int
     */
    public function getLoggedInId()
    {
        return (int)$this->_getData(LoggedInInterface::LOGGEDIN_ID);
    }

    /**
     * @param int $loggedInId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setLoggedInId($loggedInId)
    {
        return $this->setData(LoggedInInterface::LOGGEDIN_ID, $loggedInId);
    }

    /**
     * @return string
     */
    public function getLoggedInTime()
    {
        return (string)$this->_getData(LoggedInInterface::LOGGEDIN_TIME);
    }

    /**
     * @return string
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setLoggedInTime($time)
    {
        return $this->setData(LoggedInInterface::LOGGEDIN_TIME, $time);
    }

    /**
     * @return int
     */
    public function getAdminId()
    {
        return (int)$this->_getData(LoggedInInterface::ADMIN_ID);
    }

    /**
     * @param int $adminId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setAdminId($adminId)
    {
        return $this->setData(LoggedInInterface::ADMIN_ID, (int)$adminId);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return (int)$this->_getData(LoggedInInterface::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(LoggedInInterface::CUSTOMER_ID, (int)$customerId);
    }

    /**
     * @return string
     */
    public function getAdminUsername()
    {
        return (string)$this->_getData(LoggedInInterface::ADMIN_USERNAME);
    }

    /**
     * @param string $adminUsername
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setAdminUsername($adminUsername)
    {
        return $this->setData(LoggedInInterface::ADMIN_USERNAME, $adminUsername);
    }

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return (string)$this->_getData(LoggedInInterface::ADMIN_EMAIL);
    }

    /**
     * @param string $adminEmail
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setAdminEmail($adminEmail)
    {
        return $this->setData(LoggedInInterface::ADMIN_EMAIL, $adminEmail);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return (string)$this->_getData(LoggedInInterface::CUSTOMER_NAME);
    }

    /**
     * @param string $customerName
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(LoggedInInterface::CUSTOMER_NAME, $customerName);
    }

    /**
     * @return string
     */
    public function getCustomerLastName()
    {
        return (string)$this->_getData(LoggedInInterface::CUSTOMER_LASTNAME);
    }

    /**
     * @param string $customerLastName
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerLastName($customerLastName)
    {
        return $this->setData(LoggedInInterface::CUSTOMER_LASTNAME, $customerLastName);
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return (string)$this->_getData(LoggedInInterface::CUSTOMER_EMAIL);
    }

    /**
     * @param string $customerEmail
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(LoggedInInterface::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return (int)$this->_getData(LoggedInInterface::WEBSITE_ID);
    }

    /**
     * @param int $websiteId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(LoggedInInterface::WEBSITE_ID, (int)$websiteId);
    }

    /**
     * @return string
     */
    public function getWebsiteCode()
    {
        return (string)$this->_getData(LoggedInInterface::WEBSITE_CODE);
    }

    /**
     * @param string $websiteCode
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setWebsiteCode($websiteCode)
    {
        return $this->setData(LoggedInInterface::WEBSITE_CODE, $websiteCode);
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return (string)$this->_getData(LoggedInInterface::SECRET_KEY);
    }

    /**
     * @param string $secretKey
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setSecretKey($secretKey)
    {
        return $this->setData(LoggedInInterface::SECRET_KEY, $secretKey);
    }
}
