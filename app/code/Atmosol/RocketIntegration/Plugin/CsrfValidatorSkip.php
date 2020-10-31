<?php
/**
 * This file is part of the frameless project.
 *
 * Copyright (c) atmosol
 *
 * All rights reserved.
 */

namespace Atmosol\RocketIntegration\Plugin;

/**
 * CsrfValidatorSkip class
 */
class CsrfValidatorSkip
{
    /**
     * @param \Magento\Framework\App\Request\CsrfValidator $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ActionInterface $action
     */
    public function aroundValidate($subject, \Closure $proceed, $request, $action)
    {
        if (strtolower($request->getModuleName()) == 'atmosol_rocketintegration') {
            return; // Skip CSRF check
        }
        $proceed($request, $action); // Proceed Magento 2 core functionalities
    }
}
