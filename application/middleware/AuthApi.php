<?php

namespace application\middleware;

use application\service\ApiClientService;
use application\service\AppUserAccessTokensService;
use application\util\AppUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\JWT;
use application\util\MessageUtils;
use application\util\SecurityUtil;
use application\util\SystemConstant;

class AuthApi
{

    private $appUserAccessTokensService;
    /**
     * @var ApiClientService
     */
    private $apiClientService;

    /**
     * AuthApi constructor.
     * @param null $connection
     */
    public function __construct($connection = null)
    {

        $verify = MessageUtils::getConfig('production_mode');
        if ($verify) {
            $this->appUserAccessTokensService = new AppUserAccessTokensService($connection);
            $this->apiClientService = new ApiClientService($connection);
            $this->requiredTokenAuthorization();
        }
    }

    public function __destruct()
    {
        unset($this->appUserAccessTokensService);
    }
    private function verifyApiClient()
    {
        $apiName = SecurityUtil::getReqHeaderByAtt(SystemConstant::API_NAME_ATT);
        if (!$apiName) {
            return null;
        }
        $apiClient = $this->apiClientService->findByApiName($apiName);
        if (!$apiClient) {
            return null;
        }
        //step 1
        if (!$apiClient->isStatus()) {
            return null;
        }
        //don't find in api_client_ip and return true if this api set to bypass
        if ($apiClient->isBypass()) {
            return $apiClient;
        }

        $ipAdress = AppUtil::getRealIpAddr();
        $apiClientIp = $this->apiClientService->findIpByClientIdAndIp($apiClient->getId(), $ipAdress);
        if (!$apiClientIp) {
            return null;
        }
        if (!$apiClientIp->isStatus()) {
            return null;
        }
        return $apiClient;

    }
    private function requiredTokenAuthorization()
    {
        $apiClient = $this->verifyApiClient();
        if (!$apiClient) {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => 'Api Client Not found',
            ], 401);
        }

        $jwt = SecurityUtil::decodeJWT(true, $apiClient->getApiToken());
        $payload =$jwt['payload'];
        $accessTokenInDb = $this->appUserAccessTokensService->findByToken($payload->key, true);
        $expired = null;
        if ($accessTokenInDb) {
            //check is time expired
            $expired = $accessTokenInDb['expires_at'] ? DateUtils::convertDateToTimeStamp($accessTokenInDb['expires_at']) : null;
            if ($expired) {
                if ($expired <= DateUtils::getTimeNow()) {
                    jsonResponse([
                        SystemConstant::SERVER_STATUS_ATT => false,
                        SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('error.loginSessionExpired'),
                    ], 401);
                }
            } else {
                jsonResponse([
                    SystemConstant::SERVER_STATUS_ATT => false,
                    SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('httpStatus.401'),
                ], 401);
            }
        } else {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('httpStatus.401'),
            ], 401);
        }
    }

}