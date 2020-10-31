<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Model;

use Amasty\CustomerLogin\Api\LoggedInRepositoryInterface;
use Amasty\CustomerLogin\Model\ResourceModel\LoggedIn\CollectionFactory;
use Amasty\CustomerLogin\Model\ResourceModel\LoggedIn as LoggedInResource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class LoggedInRepository implements LoggedInRepositoryInterface
{
    /**
     * Model data storage
     *
     * @var array
     */
    private $loggedIn;

    /**
     * @var LoggedInFactory
     */
    private $loggedInFactory;

    /**
     * @var LoggedInResource
     */
    private $loggedInResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        LoggedInFactory $loggedInFactory,
        LoggedInResource $loggedInResource,
        CollectionFactory $collectionFactory
    ) {
        $this->loggedInFactory = $loggedInFactory;
        $this->loggedInResource = $loggedInResource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save
     *
     * @param \Amasty\CustomerLogin\Api\Data\LoggedInInterface $loggedIn
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     */
    public function save(\Amasty\CustomerLogin\Api\Data\LoggedInInterface $loggedIn)
    {
        try {
            if ($loggedIn->getLoggedInId()) {
                $loggedIn = $this->getById($loggedIn->getLoggedInId())->addData($loggedIn->getData());
            }
            $this->loggedInResource->save($loggedIn);
            unset($this->loggedIn[$loggedIn->getLoggedInId()]);
        } catch (\Exception $e) {
            if ($loggedIn->getLoggedInId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save logged in with ID %1. Error: %2',
                        [$loggedIn->getLoggedInId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new logged in. Error: %1', $e->getMessage()));
        }

        return $loggedIn;
    }

    /**
     * Get by id
     *
     * @param int $loggedInId
     *
     * @return \Amasty\CustomerLogin\Api\Data\LoggedInInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($loggedInId)
    {
        if (!isset($this->loggedIn[$loggedInId])) {
            /** @var \Amasty\CustomerLogin\Model\LoggedIn $loggedIn */
            $loggedIn = $this->loggedInFactory->create();
            $this->loggedInResource->load($loggedIn, $loggedInId);
            if (!$loggedIn->getLoggedInId()) {
                throw new NoSuchEntityException(__('LoggedIn with specified ID "%1" not found.', $loggedInId));
            }
            $this->loggedIn[$loggedInId] = $loggedIn;
        }

        return $this->loggedIn[$loggedInId];
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        //TODO
    }
}
