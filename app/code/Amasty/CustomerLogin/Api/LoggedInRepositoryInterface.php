<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Api;

/**
 * @api
 */
interface LoggedInRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\CustomerLogin\Api\Data\LoggedInInterface $loggedIn
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function save(\Amasty\CustomerLogin\Api\Data\LoggedInInterface $loggedIn);

    /**
     * Get by id
     *
     * @param int $loggedInId
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($loggedInId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
