<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 21/3/2019
 * Time: 2:40 PM
 */

namespace application\controller;

use application\core\AppController;
use application\service\ApiClientService;
use application\service\AppUserAccessTokensService;
use application\service\AppUserService;
use application\service\LoginService;
use application\util\FilterUtils;
use application\service\AuthenService;
use application\util\i18next;
use application\util\SecurityUtil;
use application\util\SystemConstant;
use application\util\UploadUtil;

class AuthenApiController extends AppController
{
    private $appUSerService;
    private $loginService;
    private $authenService;
    /**
     * @var AppUserAccessTokensService
     */
    private $appUserAccessTokensService;
    /**
     * @var ApiClientService
     */
    private $apiClientService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->isAuthRequired = false;
        $this->loginService = new LoginService($this->getDbConn());
        $this->appUSerService = new AppUserService($this->getDbConn());
        $this->authenService = new AuthenService($this->getDbConn());
        $this->appUserAccessTokensService = new AppUserAccessTokensService($this->getDbConn());
        $this->apiClientService = new ApiClientService($this->getDbConn());
    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->loginService);
        unset($this->appUSerService);
        unset($this->authenService);
    }

    public function appUserAuthen()
    {
        $apiClientName = SecurityUtil::getReqHeaderByAtt(SystemConstant::API_NAME_ATT);
        if (!$apiClientName) {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => 'Api Client Not found',
            ], 401);
        }


        $jsonData = $this->getJsonData(false);//past true for convert object class to objec array
        $data = $this->setResponseStatus([], false, i18next::getTranslation('error.err_username_or_passwd_notfound'));
        if ($jsonData) {
            $username = FilterUtils::filterVarString($jsonData->_u);
            $userpwd = FilterUtils::filterVarString($jsonData->_p);
            $data = $this->authenService->userAuthenApi($username, $userpwd);
            if ($data[SystemConstant::SERVER_STATUS_ATT] && $data[SystemConstant::USER_API_KEY_ATT] != null) {
                $appuserData = $this->appUSerService->findByUsername($username);
                if ($appuserData) {
                    $apiClient = $this->apiClientService->findByApiName($apiClientName);

                    if (!$apiClient) {
                        jsonResponse([
                            SystemConstant::SERVER_STATUS_ATT => false,
                            SystemConstant::SERVER_MSG_ATT => 'Api Client Not found',
                        ], 401);
                    }

                    $data['userData'] = array(
//                        'apiKey' => $data[SystemConstant::USER_API_KEY_ATT],
                        'apiKey' => $this->appUserAccessTokensService->createNewToken($data[SystemConstant::USER_API_KEY_ATT], $appuserData->getId(), $apiClient->getId(), $apiClient->getApiToken()),
                        'uid' => $appuserData->getId(),
                        'img' => UploadUtil::getUserAvatarApi($appuserData->getImgName(), $appuserData->getCreatedDate()),
                        'uname' => $appuserData->getUsername(),
                        'email' => $appuserData->getEmail(),
                    );
                    unset($data[SystemConstant::USER_API_KEY_ATT]);
                }
            }
        }
        jsonResponse($data);
    }
    public function appUserLogout()
    {
        $this->appUserAccessTokensService->logoutAction();
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT =>  true,
        ]);
    }

    public function checkUserAuthenApi()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $this->jsonResponse(
            $this->appUSerService->findByIdArr($uid)
        );
    }

}