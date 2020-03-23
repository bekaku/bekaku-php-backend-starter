<?php


namespace application\middleware;


use application\util\ControllerUtil;
use application\util\SecurityUtil;
use application\util\SystemConstant;

class SetLocale
{
    public function __construct($connection = null)
    {
        $headerLocale = SecurityUtil::getReqHeaderByAtt(SystemConstant::HEADER_LOCALE_ATT);
        ControllerUtil::i18nextInit($headerLocale != null ? $headerLocale : ControllerUtil::getCurrentLocale());
    }
}