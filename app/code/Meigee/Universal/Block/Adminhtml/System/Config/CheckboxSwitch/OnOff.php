<?php
namespace Meigee\Universal\Block\Adminhtml\System\Config\CheckboxSwitch;

class OnOff extends \Meigee\Universal\Block\Adminhtml\System\Config\CheckboxSwitch
{
    function getOnLabel()
    {
        return __('On');
    }
    function getOffLabel()
    {
        return __('Off');
    }
}