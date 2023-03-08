<?php


namespace application\controller;


use application\core\AppController;
use application\service\AccessTokenService;
use application\service\ApiClientService;
use application\service\AuthService;
use application\service\UserService;
use application\util\ControllerUtil;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SecurityUtil;
use application\util\SystemConstant;
use application\util\UploadUtil;

class AuthController extends AppController
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var AccessTokenService
     */
    private $accessTokenService;
    /**
     * @var AuthService
     */
    private $authService;
    /**
     * @var ApiClientService
     */
    private $apiClientService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->userService = new UserService($this->getDbConn());
        $this->authService = new AuthService($this->getDbConn());
        $this->accessTokenService = new AccessTokenService($this->getDbConn());
        $this->apiClientService = new ApiClientService($this->getDbConn());
    }

    public function signin()
    {
        $apiClientName = SecurityUtil::getReqHeaderByAtt(SystemConstant::API_NAME_ATT);
        if (!$apiClientName) {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => 'Api Client Not found',
            ], 401);
        }

        $apiClient = $this->apiClientService->findByApiName($apiClientName);
        if (!$apiClient) {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => 'Api Client Not found',
            ], 401);
        }

        $jsonData = $this->getJsonData();//past true for convert object class to objec array
        $data = $this->setResponseStatus([], false, i18next::getTranslation('error.err_username_or_passwd_notfound'));
        if ($jsonData) {
            $email = FilterUtils::filterVarString($jsonData->_u);
            $userpwd = FilterUtils::filterVarString($jsonData->_p);

            $data = $this->authService->signin($email, $userpwd);

            if ($data->status && $data->apiKey != null) {
                $appuserData = $this->userService->findByEmail($email);
                if ($appuserData) {

                    $responseData = $this->userService->findUserDataById($appuserData->id);
                    $responseData->apiKey = $this->accessTokenService->createNewToken($data->apiKey, $appuserData->id, $apiClient->id, $apiClient->api_token);

                    $data->userData = $responseData;
                    unset($data->apiKey);
                }
            }
        }
        jsonResponse($data);

    }

    public function userLogout()
    {
        $this->accessTokenService->logoutAction();
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT => true,
            SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('error.logoutSuccess'),
        ]);
    }

    public function userCheckAuth()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
//        jsonResponse($this->userService->findUserDataById($uid));
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT => true,
            'userData' => $this->userService->findUserDataById($uid),
        ]);
    }

    public function changePwd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $this->pushDataToView = $this->getDefaultResponse(false);
        $jsonData = $this->getJsonData();
        if (!empty($jsonData) && !empty($uid)) {

            //validate old pwd
            $user = $this->userService->findUserDataById($uid);
            $userpwd = FilterUtils::filterVarString($jsonData->oldPassword);

            $data = $this->authService->signin($user->email, $userpwd);
            if ($data->status && $data->apiKey != null) {

                $newPwd = FilterUtils::filterVarString($jsonData->_p);
                $randomSalt = ControllerUtil::getRadomSault();
                $effectRow = $this->userService->update([
                    'password' => ControllerUtil::genHashPassword($newPwd, $randomSalt),
                    'salt' => $randomSalt
                ], ['id' => $uid]);

                if ($effectRow) {
                    //logout device
                    if ($jsonData->logoutAll) {
                        $this->accessTokenService->logoutAllAction();
                    } else {
                        $this->accessTokenService->logoutAction();
                    }

                    $this->pushDataToView = $this->setResponseStatus([], true, i18next::getTranslation(('success.changePasswordOk')));
                } else {
                    $this->pushDataToView = $this->setResponseStatus([], false, i18next::getTranslation('error.error_something_wrong'));
                }
            }else{
                $this->pushDataToView = $this->setResponseStatus([], false, i18next::getTranslation('error.passwordCurrentWrong'));
            }
        }
        jsonResponse($this->pushDataToView);
    }
}