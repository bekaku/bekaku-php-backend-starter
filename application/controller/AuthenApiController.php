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
    public function __construct($databaseConnection){
        $this->setDbConn($databaseConnection);
        $this->isAuthRequired = false;
        $this->loginService = new LoginService($this->getDbConn());
        $this->appUSerService = new AppUserService($this->getDbConn());
        $this->authenService = new AuthenService($this->getDbConn());
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
        $jsonData = $this->getJsonData(false);//past true for convert object class to objec array
        $data = $this->setResponseStatus(array(), false, i18next::getTranslation('error.err_username_or_passwd_notfound'));
        if($jsonData){
            $username = FilterUtils::filterVarString($jsonData->_u);
            $userpwd = FilterUtils::filterVarString($jsonData->_p);

            $data = $this->authenService->userAuthenApi($username, $userpwd);
            if($data[SystemConstant::SERVER_STATUS_ATT] && $data[SystemConstant::USER_API_KEY_ATT]!=null){
                $appuserData = $this->appUSerService->findByUsername($username);
                if($appuserData){
                    $data['userData'] = array(
                        'apiKey' => $data[SystemConstant::USER_API_KEY_ATT],
                        'uid' => $appuserData->getId(),
                        'img' => UploadUtil::getUserAvatarApi($appuserData->getImgName(), $appuserData->getCreatedDate()),
                        'uname' => $appuserData->getUsername(),
                        'email' => $appuserData->getEmail(),
                    );
                    unset($data[SystemConstant::USER_API_KEY_ATT]);
                }
            }
        }

        echo json_encode($data);
    }

    public function checkUserAuthenApi()
    {
        $authorizationData = SecurityUtil::requiredTokenAuthorization($this->authenService, new ApiClientService($this->getDbConn()));
        $this->jsonResponse($authorizationData);
    }

}