<?php

namespace application\middleware;

use application\service\PermissionService;
use application\util\i18next;
use application\util\MessageUtils;
use application\util\SecurityUtil;
use application\util\SystemConstant;

class PermissionGrant
{

    private $permissionService;

    /**
     * AuthApi constructor.
     * @param null $connection
     * @param $permissionName
     */
    public function __construct($connection = null, $permissionName=null)
    {
        $mode = MessageUtils::getConfig('production_mode');
        $verify = $mode == SystemConstant::PRODUCTION_MODE_PRODUCTION ? true : false;
        if ($verify) {
            $this->permissionService = new PermissionService($connection);
            $isPermised = $this->permissionService->isHavePermission(SecurityUtil::getAppuserIdFromJwtPayload(), $permissionName);
            if (!$isPermised) {
                jsonResponse([
                    SystemConstant::SERVER_STATUS_ATT => false,
                    SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('error.permissionDeny'),
                ], 401);
            }
        }
    }
    public function __destruct()
    {
        unset($this->permissionService);
    }


}