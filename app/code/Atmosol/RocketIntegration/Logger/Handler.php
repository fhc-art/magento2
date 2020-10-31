<?php
/**
 * This file is part of the frameless project.
 *
 * Copyright (c) atmosol
 *
 * All rights reserved.
 */

namespace Atmosol\RocketIntegration\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

/**
 * Handler class
 */
class Handler extends Base
{
    /** @var int */
    protected $loggerType = MonologLogger::INFO;
    /** @var string */
    protected $fileName = '/var/log/rocket.log';
}
