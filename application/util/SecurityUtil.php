<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 22/3/2019
 * Time: 10:48 AM
 */

namespace application\util;


use application\core\Route;
use application\service\ApiClientService;
use application\service\AppPermissionService;
use application\service\AppUserService;
use application\service\AuthenService;

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
    public static function decodeJWT($verify = false, $secretServerkey=null)
    {
        $authorization = self::getReqHeaderByAtt('Authorization');
        if (!$authorization) {
            $authorization = (isset($_GET['key']) ? "key=" . FilterUtils::filterVarString($_GET['key']) : null);
        }
        $jwt = null;
        if ($authorization) {
            $jwtKeyArr = explode("key=", $authorization);
            $jwtToken = null;
            if (count($jwtKeyArr) > 0) {
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

    private static function verifyApiClient($jwtToken, ApiClientService $apiClientService, $payLoad)
    {
//        print_r($payLoad);
        $apiName = $payLoad && isset($payLoad[SystemConstant::API_NAME_ATT]) ? $payLoad[SystemConstant::API_NAME_ATT] : null;

        if (!$apiName) {
            return false;
        }

        $apiClient = $apiClientService->findByApiName($apiName);

        if (!$apiClient) {
            return false;
        }
//        echoln("id :".$apiClient->getId().
//        ', apiName : '.$apiClient->getApiName().
//        ", token : ".$apiClient->getApiToken().
//        ', bypass : '.$apiClient->isBypass().
//        ', status : '.$apiClient->isStatus());

        //step 1
        $jwt = JWT::decode($jwtToken, $apiClient->getApiToken(), true);
        if (!$jwt) {
            return false;
        }
        $payLoad = isset($jwt['payload']) ? $jwt['payload'] : null;
        if (!$payLoad) {
            return false;
        }
        if (!$jwt['status']) {
            return false;
        }
        if (!$apiClient->isStatus()) {
            return false;
        }
        //don't find in api_client_ip and return true if this api set to bypass
        if ($apiClient->isBypass()) {
            return true;
        }
        $ipAdress = AppUtil::getRealIpAddr();
        $apiClientIp = $apiClientService->findIpByClientIdAndIp($apiClient->getId(), $ipAdress);
        if (!$apiClientIp) {
            return false;
        }
        if (!$apiClientIp->isStatus()) {
            return false;
        }
//        echoln("id :".$apiClientIp->getId().
//            ', getApiClient : '.$apiClientIp->getApiClient().
//            ", getApiAddress : ".$apiClientIp->getApiAddress());


        return true;

    }


    public static function isPermission($connection, $permission)
    {
        $permissionService = new AppPermissionService($connection);
        $isPermised = $permissionService->checkPermissionByUserSessionId($permission);

        unset($permissionService);
        return $isPermised;
    }

    public static function checkNavPermissionByUrl($connection, $url)
    {
        $routFind = AppUtil::findObjectInArray(Route::$routeList, $url, 'url');
        if (!empty($routFind)) {
            $permissionRequir = $routFind['permission'];
            if (!empty($permissionRequir)) {
                return self::isPermission($connection, $permissionRequir);
            }
        }
        return true;
    }
    private static function defaultDenyResponse()
    {
        return array(
            SystemConstant::SERVER_STATUS_ATT => false,
            SystemConstant::SERVER_STATUS_CODE_ATT => 401,
            SystemConstant::SERVER_MSG_ATT => "JWT Signature verification failed",
//            "request" =>self::getRequestHeaders(),
//            "ip" => AppUtil::getRealIpAddr()
        );
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