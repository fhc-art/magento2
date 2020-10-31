<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


declare(strict_types=1);

namespace Amasty\CustomerLogin\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    protected $pathPrefix = 'amcustomerlogin/';

    const NEW_TAB = 'general/new_tab';

    const ALLOW_SELECT_STORE = 'general/allow_select_store';

    /**
     * @return bool
     */
    public function isNewTab(): bool
    {
        return $this->isSetFlag(self::NEW_TAB);
    }

    /**
     * @return bool
     */
    public function isAllowSelectStore(): bool
    {
        return $this->isSetFlag(self::ALLOW_SELECT_STORE);
    }
}
