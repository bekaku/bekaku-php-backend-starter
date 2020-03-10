<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 2/4/2019
 * Time: 4:55 PM
 */

namespace application\controller;


use application\core\AppController;
use application\model\AppUser;
use application\model\AppUserRole;
use application\model\AppUserRoleRoles;
use application\service\AppUserLoginAttemptsService;
use application\service\AppUserLoginService;
use application\service\AppUserRoleRolesService;
use application\service\AppUserRoleService;
use application\service\AppUserService;
use application\service\AuthenService;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SecurityUtil;
use application\util\SystemConstant;
use application\util\UploadUtil;
use application\validator\AppUserValidator;

class AppUserController extends AppController
{
    private $appUserService;
    private $appUserRoleService;
    private $appUserRoleRolesService;
    private $authenService;
    private $appUserLoginService;
    private $appUserLoginAttemptsService;

    public function __construct($databaseConnection)
    {
        $this->isAuthRequired = true;
        $this->setDbConn($databaseConnection);
        $this->appUserService = new AppUserService($this->getDbConn());
        $this->appUserRoleService = new AppUserRoleService($this->getDbConn());
        $this->appUserRoleRolesService = new AppUserRoleRolesService($this->getDbConn());
        $this->authenService = new AuthenService($this->getDbConn());
        $this->appUserLoginService = new AppUserLoginService($this->getDbConn());
        $this->appUserLoginAttemptsService = new AppUserLoginAttemptsService($this->getDbConn());
    }

    public function __destruct()
    {
        unset($this->appUserService);
        unset($this->appUserRoleService);
        unset($this->appUserRoleRolesService);
        unset($this->authenService);
        unset($this->appUserLoginService);
        unset($this->appUserLoginAttemptsService);
    }

    public function crudList()
    {

        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new AppUser());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->appUserService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->appUserService->getTotalPaging();
        $this->jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse();
        $appUser = new AppUser();
        $appUser->populatePostDataRestApi($jsonData);

        $validator = new AppUserValidator($appUser);
        $this->pushDataToView['data'] = $jsonData;

        //Custom Validate
        //validate duplicate user name
        $appUserfindUsername = $this->appUserService->findByUsername($appUser->getUsername());
        if (!empty($appUserfindUsername)) {
            $validator->addError('username', 'The username ' . $appUser->getUsername() . ' has already been taken. Please choose different username  ');
        }
        //validate duplicate email
        $appUserfindEmail = $this->appUserService->findByEmail($appUser->getEmail());
        if (!empty($appUserfindEmail)) {
            $validator->addError('email', 'The email ' . $appUser->getEmail() . ' has already been taken. Please choose different email  ');
        }

        $errors = $validator->getValidationErrors();
        if ($errors) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, $errors);
        } else {
            $appUser = $this->initBaseCreateDataRestApi($appUser, $uid);
            $randomSalt = ControllerUtil::getRadomSault();
            $appUser->setLoginPassword(ControllerUtil::genHashPassword($appUser->getLoginPassword(), $randomSalt));
            $appUser->setSalt($randomSalt);

            $lastInsertId = $this->appUserService->createByObject($appUser);
            if ($lastInsertId) {
                $this->addAppUserRoleRoles($lastInsertId, $jsonData->roles);
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.insert_succesfull')));
            } else {
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
            }
        }
        $this->jsonResponse($this->pushDataToView);
    }

    private function addAppUserRoleRoles($appUserId, $roles)
    {

        $appUserRoleRoles = new AppUserRoleRoles();
        $appUserRoleRoles = $this->initBaseCreateDataRestApi($appUserRoleRoles, $appUserId);
        $appUserRoleRoles->setAppUser($appUserId);

        foreach ($roles AS $role) {
            if (FilterUtils::isValidInt($role)) {
                $appUserRoleRoles->setAppUserRole($role);
                $this->appUserRoleRolesService->createByObject($appUserRoleRoles);
            }
        }

    }

    public function crudEdit()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();

        $id = FilterUtils::validateGetInt(ControllerUtil::encodeParamId(AppUserRole::$tableName));
        if (AppUtil::isEmpty($id)) {
            ControllerUtil::f404Static();
        }
        $appUserOld = $this->appUserService->findById($id);
        if (!$appUserOld) {
            ControllerUtil::f404Static();
        }

        $this->pushDataToView = $this->getDefaultResponse();

        $jsonData = $this->getJsonData(false);
        $appUser = new AppUser();
        $appUser->populatePostDataRestApi($jsonData);
        $appUser->setId($id);

        $validator = new AppUserValidator($appUser);
        //validate duplicate user name
        if ($appUserOld->getUsername() != $appUser->getUsername()) {
            $appUserfindUsername = $this->appUserService->findByUsername($appUser->getUsername());
            if (!empty($appUserfindUsername)) {
                $validator->addError('username', 'The username ' . $appUser->getUsername() . ' has already been taken. Please choose different username  ');
            }
        }

        //validate duplicate email
        if ($appUserOld->getEmail() != $appUser->getEmail()) {
            $appUserfindEmail = $this->appUserService->findByEmail($appUser->getEmail());
            if (!empty($appUserfindEmail)) {
                $validator->addError('email', 'The email ' . $appUser->getEmail() . ' has already been taken. Please choose different email  ');
            }
        }


        $errors = $validator->getValidationErrors();
        if ($errors) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, $errors);
        } else {
            $appUser = $this->initBaseUpdateDataRestApi($appUser, $uid);

            //delete app_user_role_roles before update app_user
            $this->deleteAppUserRoleRoles($appUser->getId());
            //add current role select
            $this->addAppUserRoleRoles($appUser->getId(), $jsonData->roles);

            $effectRow = $this->appUserService->updateByObject($appUser, array('id' => $appUser->getId()));
            if ($effectRow) {
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
            } else {
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
            }
        }
        $this->jsonResponse($this->pushDataToView);
    }

    public function crudDelete()
    {
        $id = FilterUtils::validateGetInt(ControllerUtil::encodeParamId(AppUserRole::$tableName));
        if (AppUtil::isEmpty($id)) {
            ControllerUtil::f404Static();
        }
        $appUser = $this->appUserService->findById($id);
        if (!$appUser) {
            ControllerUtil::f404Static();
        }

        //delete img
        if (!AppUtil::isEmpty($appUser->getImgName())) {
            UploadUtil::delImgfileFromYearMonthFolder($appUser->getImgName(), $appUser->getCreatedDate());
        }

        //delete from appUserLogin
        $this->appUserLoginService->deleteByUserId($appUser->getId());

        //delete from appUserLoginAttempts
        $this->appUserLoginAttemptsService->deleteByUserId($appUser->getId());

        //delete app_user_role_roles before delete app_user
        $this->deleteAppUserRoleRoles($id);
        //then delete permission
        $effectRow = $this->appUserService->deleteById($id);
        if (!$effectRow) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
        } else {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.delete_succesfull')));
        }

        $this->jsonResponse($this->pushDataToView);
    }

    private function deleteAppUserRoleRoles($appUserId)
    {

        $appUserRoleRolesList = $this->appUserRoleRolesService->findAppUserRoleRolesByApUser($appUserId);
        if ($appUserRoleRolesList) {
            foreach ($appUserRoleRolesList as $appUserRoleRoles) {
                $roleRolesId = $appUserRoleRoles['id'];
                $this->appUserRoleRolesService->deleteById($roleRolesId);
            }
        }
    }

    public function changePwd()
    {

        $this->pushDataToView = $this->getDefaultResponse();
        $jsonData = $this->getJsonData(false);

        $randomSalt = ControllerUtil::getRadomSault();
        $data['login_password'] = ControllerUtil::genHashPassword($jsonData->login_password, $randomSalt);
        $data['salt'] = $randomSalt;

        $effectRow = $this->appUserService->update($data, array('id' => $jsonData->id), 'AND');
        if ($effectRow) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
        } else {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
        }
        $this->jsonResponse($this->pushDataToView);
    }

    public function uploadImage()
    {

        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $this->pushDataToView = $this->getDefaultResponse();
        $appUser = $this->appUserService->findById($uid);

        if ($uid && $appUser) {

            //delete the old one before upload new img
            if (!AppUtil::isEmpty($appUser->getImgName())) {
                UploadUtil::delImgfileFromYearMonthFolder($appUser->getImgName(), $appUser->getCreatedDate());
            }

            //upload new img
            $imagNameGenerate = UploadUtil::getUploadFileName($uid);
            if (is_uploaded_file($_FILES[SystemConstant::APP_IMAGE_FILE_UPLOAD_ATT]['tmp_name'])) {
                $imgName = UploadUtil::uploadImgFiles($_FILES[SystemConstant::APP_IMAGE_FILE_UPLOAD_ATT], $appUser->getCreatedDate(), 0, $imagNameGenerate);
                if ($imgName) {
                    $appUser->setImgName($imgName);
                    $this->pushDataToView['img_api'] = UploadUtil::getUserAvatarApi($imgName, $appUser->getCreatedDate());
                    $this->appUserService->updateByObject($appUser, array('id' => $appUser->getId()));
                }
            }
        } else {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
        }
        //end upload file

        $this->jsonResponse($this->pushDataToView);
    }
}