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
    public static function getRequestHeaders()
    {
        return apache_request_headers();
    }

    public static function decodeJWTAuthorizationData($header, ApiClientService $apiClientService = null, $isChekApi = true)
    {
        $authKey = isset($header['Authorization']) ? $header['Authorization'] : "";
//        $authKey ="key=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W3siYXBpQ2xpZW50IjoiY3R0X2xvZ2lzdGljIn1d._RMZcdLabS2AiXBoB6PHt3fEmKD7jh_i_bdt3ldQVHs";
        $authorization = FilterUtils::filterVarString($authKey);

        $jwtToken = null;
        if ($authorization) {

            $jwtKeyArr = explode("key=", $authorization);
            if (count($jwtKeyArr) > 0) {
                $jwtToken = $jwtKeyArr[1];
            }
            //step 1
            $jwt = JWT::decode($jwtToken, null, false);

            if (!$jwt) {
                return self::defaultDenyResponse();
            }

            //if not required api client verify return payload
            if (!$isChekApi) {
                return $jwt;
            }

            $payLoad = isset($jwt['payload']) ? $jwt['payload'] : null;
            if (!$payLoad) {
                return self::defaultDenyResponse();
            }

            //if jwt req timeout
            if (isset($payLoad['exp']) && $payLoad['exp'] <= DateUtils::getTimeNow()) {
                $response = self::defaultDenyResponse();
                $response[SystemConstant::SERVER_MSG_ATT] = 'JWT Request timeout';
                return $response;
            }
            $verify = self::verifyApiClient($jwtToken, $apiClientService, $payLoad);

            return $verify ? $jwt : self::defaultDenyResponse();
        } else {
            return self::defaultDenyResponse();
        }
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

    public static function requiredTokenAuthorization(AuthenService $authenService, ApiClientService $apiClientService)
    {
        $authorizationData = self::decodeJWTAuthorizationData(self::getRequestHeaders(), $apiClientService);

        if (!$authorizationData[SystemConstant::SERVER_STATUS_ATT]) {
            $data[SystemConstant::SERVER_STATUS_ATT] = $authorizationData[SystemConstant::SERVER_STATUS_ATT];
            $data[SystemConstant::SERVER_MSG_ATT] = $authorizationData[SystemConstant::SERVER_MSG_ATT];
            $data[SystemConstant::SERVER_STATUS_CODE_ATT] = 401;
            $data[SystemConstant::LOGIN_SESSION_EXPIRE] = true;
            ControllerUtil::f401Static(null, $data);
        } else {
            $payload = $authorizationData['payload'];
            if (isset($payload[SystemConstant::JWT_USER_ID_ATT]) && isset($payload['key'])) {
                $isTokenAuthenCheck = $authenService->checkUserAuthenApi($payload[SystemConstant::JWT_USER_ID_ATT], $payload['key']);
                if (!$isTokenAuthenCheck) {
                    $data[SystemConstant::LOGIN_SESSION_EXPIRE] = true;
                    $data[SystemConstant::SERVER_STATUS_ATT] = false;
                    $data[SystemConstant::SERVER_MSG_ATT] = "Session Expired Login again";
                    $data[SystemConstant::SERVER_STATUS_CODE_ATT] = 401;
                    ControllerUtil::f401Static(null, $data);
                }
            } else {
                $data[SystemConstant::LOGIN_SESSION_EXPIRE] = true;
                $data[SystemConstant::SERVER_STATUS_ATT] = false;
                $data[SystemConstant::SERVER_MSG_ATT] = "Session Expired Login again";
                $data[SystemConstant::SERVER_STATUS_CODE_ATT] = 401;
                ControllerUtil::f401Static(null, $data);
            }
        }

        return $authorizationData;
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

    public static function isPermissionByUserId($connection, $userId, $permission)
    {
        $permissionService = new AppPermissionService($connection);
        $isPermised = $permissionService->checkPermissionByUserId($userId, $permission);
        unset($permissionService);
        return $isPermised;
    }

    public static function getAppUserLoged($connection)
    {
        $appUserServices = new AppUserService($connection);
        $appUser = $appUserServices->findById(ControllerUtil::getUserIdSession());
        unset($appUserServices);
        return $appUser;
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
        $jwt = self::decodeJWTAuthorizationData(self::getRequestHeaders(), null, false);
        return isset($jwt['payload']) ? $jwt['payload'] : null;
    }

    public static function getAppuserIdFromJwtPayload()
    {
        $jwtPaylaod = self::getJwtPayload();
        return isset($jwtPaylaod[SystemConstant::JWT_USER_ID_ATT]) ? $jwtPaylaod[SystemConstant::JWT_USER_ID_ATT] : null;
    }
}