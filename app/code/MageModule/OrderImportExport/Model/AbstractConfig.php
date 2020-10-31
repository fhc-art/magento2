<?php
/**
 * Copyright (c) 2019 MageModule, LLC: All rights reserved
 *
 * LICENSE: This source file is subject to our standard End User License
 * Agreeement (EULA) that is available through the world-wide-web at the
 * following URI: https://www.magemodule.com/magento2-ext-license.html.
 *
 *  If you did not receive a copy of the EULA and are unable to obtain it through
 *  the web, please send a note to admin@magemodule.com so that we can mail
 *  you a copy immediately.
 *
 * @author         MageModule admin@magemodule.com
 * @copyright      2019 MageModule, LLC
 * @license        https://www.magemodule.com/magento2-ext-license.html
 */

namespace MageModule\OrderImportExport\Model;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class AbstractConfig
 *
 * @package MageModule\OrderImportExport\Model
 */
abstract class AbstractConfig extends \Magento\Framework\Model\AbstractModel
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
     * AbstractConfig constructor.
     *
     * @param Config\Context                                               $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $defaultOptions
     * @param array                                                        $data
     */
    public function __construct(
        \MageModule\OrderImportExport\Model\Config\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $defaultOptions = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->xmlPath        = $context->getXmlPath();
        $this->resourceConfig = $context->getResourceConfig();
        $this->serializer     = $context->getSerializer();

        if (is_array($defaultOptions)) {
            $this->addData($defaultOptions);
        }

        $value = $scopeConfig->getValue(
            $this->xmlPath,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        try {
            $value = $this->serializer->unserialize($value);
        } catch (\Exception $e) {
            $value = [];
        }

        if (is_array($value)) {
            $this->addData($value);
        }
    }

    /**
     * @return $this
     */
    public function saveConfig()
    {
        try {
            $value = $this->serializer->serialize($this->getData());
        } catch (\Exception $e) {
            $value = '';
        }

        $this->resourceConfig->saveConfig(
            $this->xmlPath,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        return $this;
    }
}
