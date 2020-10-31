<?php

namespace MageModule\OrderImportExport\Model\Config;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Context
 *
 * @package MageModule\OrderImportExport\Model\Config
 */
class Context extends \Magento\Framework\Model\Context
{
    /**
     * @var string
     */
    private $xmlPath;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $resourceConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Context constructor.
     *
     * @param string                                                $xmlPath
     * @param SerializerInterface                                   $serializer
     * @param \Magento\Config\Model\ResourceModel\Config            $resourceConfig
     * @param \Magento\Framework\Event\ManagerInterface             $eventDispatcher
     * @param \Magento\Framework\App\CacheInterface                 $cacheManager
     * @param \Magento\Framework\App\State                          $appState
     * @param \Magento\Framework\Model\ActionValidator\RemoveAction $actionValidator
     * @param \Psr\Log\LoggerInterface                              $logger
     */
    public function __construct(
        $xmlPath,
        SerializerInterface $serializer,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Model\ActionValidator\RemoveAction $actionValidator,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct(
            $logger,
            $eventDispatcher,
            $cacheManager,
            $appState,
            $actionValidator
        );

        $this->xmlPath        = $xmlPath;
        $this->resourceConfig = $resourceConfig;
        $this->serializer     = $serializer;
    }

    /**
     * @return string
     */
    public function getXmlPath()
    {
        return $this->xmlPath;
    }

    /**
     * @return \Magento\Config\Model\ResourceModel\Config
     */
    public function getResourceConfig()
    {
        return $this->resourceConfig;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }
}
