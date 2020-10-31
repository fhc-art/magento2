<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Api\Data;

interface LoggedInInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const LOGGEDIN_ID = 'logged_in_id';

    const LOGGEDIN_TIME = 'logged_in_time';

    const ADMIN_ID = 'admin_id';

    const ADMIN_USERNAME = 'admin_username';

    const ADMIN_EMAIL = 'admin_email';

    const CUSTOMER_ID = 'customer_id';

    const CUSTOMER_NAME = 'customer_name';

    const CUSTOMER_LASTNAME = 'customer_lastname';

    const CUSTOMER_EMAIL = 'customer_email';

    const WEBSITE_ID = 'website_id';

    const WEBSITE_CODE = 'website_code';

    const SECRET_KEY = 'secret_key';
    /**#@-*/

    /**
     * @return int
     */
    public function getLoggedInId();

    /**
     * @param int $loggedInId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setLoggedInId($loggedInId);

    /**
     * string $time
     *
     * @return string
     */
    public function setLoggedInTime($time);

    /**
     * @return string
     */
    public function getLoggedInTime();

    /**
     * @return int
     */
    public function getAdminId();

    /**
     * @param int $adminId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setAdminId($adminId);

    /**
     * @return string
     */
    public function getAdminUsername();

    /**
     * @param string $adminUsername
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setAdminUsername($adminUsername);

    /**
     * @return string
     */
    public function getAdminEmail();

    /**
     * @param string $adminEmail
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setAdminEmail($adminEmail);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @param string $customerName
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerName($customerName);

    /**
     * @return string
     */
    public function getCustomerLastName();

    /**
     * @param string $customerLastName
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerLastName($customerLastName);

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @param string $customerEmail
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param int $websiteId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * @return string
     */
    public function getWebsiteCode();

    /**
     * @param string $websiteCode
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setWebsiteCode($websiteCode);

    /**
     * @return string
     */
    public function getSecretKey();

    /**
     * @param string $secretKey
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function setSecretKey($secretKey);
}
