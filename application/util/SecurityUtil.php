<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 22/3/2019
 * Time: 10:48 AM
 */

namespace application\util;

class SecurityUtil
{
    const PERMISSION_GRANT_MIDDLEWARE = 'PermissionGrant';

    public static function getRequestHeaders()
    {
        return apache_request_headers();
    }

    public static function getReqHeaderByAtt($attName = null)
    {
        $headers = self::getRequestHeaders();
        return $attName != null && !empty($headers) && isset($headers[$attName]) ? FilterUtils::filterVarString($headers[$attName]) : null;
    }

    public static function decodeJWT($verify = false, $secretServerkey = null)
    {
        $authorization = self::getReqHeaderByAtt('Authorization');
        if (!$authorization) {
            $authorization = (isset($_GET['Bearer']) ? "Bearer " . FilterUtils::filterVarString($_GET['Bearer']) : null);
        }
        $jwt = null;
        if ($authorization) {
            $jwtKeyArr = explode("Bearer ", $authorization);
            $jwtToken = null;
            if (count($jwtKeyArr) == 2) {
                $jwtToken = $jwtKeyArr[1];
            }
            $jwt = JWT::decode($jwtToken, $secretServerkey, $verify);
            if ($verify) {
                $payLoad = $jwt['payload'];
                if (!empty($payLoad)) {
                    if ($payLoad->exp <= DateUtils::getTimeNow()) {
                        jsonResponse([
                            SystemConstant::SERVER_STATUS_ATT => false,
                            SystemConstant::SERVER_MSG_ATT => 'JWT Request timeout',
                        ], 401);
                    }
                } else {
                    jsonResponse([
                        SystemConstant::SERVER_STATUS_ATT => false,
                        SystemConstant::SERVER_MSG_ATT => 'JWT Signature verification failed',
                    ], 401);
                }
            }

        } else {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => 'JWT Signature verification failed',
            ], 401);
        }
        return $jwt;
    }

    public static function getJwtPayload()
    {
        $jwt = self::decodeJWT(false);
        return isset($jwt['payload']) ? $jwt['payload'] : null;
    }

    public static function getAppuserIdFromJwtPayload()
    {
        $jwtPaylaod = self::getJwtPayload();
        return $jwtPaylaod && isset($jwtPaylaod->uid) ? $jwtPaylaod->uid : null;
    }
}