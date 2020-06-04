<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-04 15:28:09
 */

namespace application\controller;

use application\core\AppController;
use application\service\AccessTokenService;
use application\service\RoleService;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\MessageUtils;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\User;
use application\service\UserService;
use application\util\UploadUtil;
use application\validator\UserValidator;

class UserController extends AppController
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var RoleService
     */
    private $roleService;
    /**
     * @var AccessTokenService
     */
    private $accessTokenService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->userService = new UserService($this->getDbConn());
        $this->roleService = new RoleService($this->getDbConn());
        $this->accessTokenService = new AccessTokenService($this->getDbConn());
    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->userService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new User());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->userService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->userService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $entity = new User($jsonData, $uid, false);
            $validator = new UserValidator($entity);

            //Custom Validate
            //validate duplicate user name
            $appUserfindUsername = $this->userService->findByUsername($jsonData->username);
            if (!empty($appUserfindUsername)) {
                $validator->addError('username', i18next::getTranslation('error.duplicateEmail', ['email' => $jsonData->username]));
            }
            //validate duplicate email
            $appUserfindEmail = $this->userService->findByEmail($jsonData->email);
            if (!empty($appUserfindEmail)) {
                $validator->addError('email', i18next::getTranslation('error.duplicateUsername', ['username' => $jsonData->email]));
            }
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null));
            } else {
                $randomSalt = ControllerUtil::getRadomSault();
                $entity->password = ControllerUtil::genHashPassword($entity->password, $randomSalt);
                $entity->salt = $randomSalt;
                $entity->image = null;

                $lastInsertId = $this->userService->createByObject($entity);
                if ($lastInsertId) {
                    //create user_role
                    $userRoles = isset($jsonData->userRoles) ? $jsonData->userRoles : null;
                    $this->createRoles($userRoles, $lastInsertId);
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->userService->findUserDataById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    private function createRoles($userRoles, $uid)
    {
        if ($userRoles) {

            //delete old
            $this->roleService->deleteUserRoleByUserId($uid);
            foreach ($userRoles AS $r) {
                $role = $this->roleService->findById($r);
                if ($role) {
                    $this->roleService->createUserRoleByArray([
                        'role' => $r,
                        'user' => $uid,
                    ]);
                }
            }
        }
    }

    public function crudReadSingle()
    {
        $id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);
        $this->pushDataToView = $this->getDefaultResponse(false);
        $item = null;
        if ($id > 0) {
            $item = $this->userService->findUserDataById($id);
            if ($item) {
                $this->pushDataToView = $this->getDefaultResponse(true);
            }
        }
        $this->pushDataToView[SystemConstant::ENTITY_ATT] = $item;
        jsonResponse($this->pushDataToView);
    }

    public function crudEdit()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {

            $user = new User($jsonData, $uid, true);
            $validator = new UserValidator($user);
            $appUserOld = $this->userService->findById($user->id);
            if (!$appUserOld) {
                ControllerUtil::f404Static();
            }

            //validate duplicate user name
            if ($appUserOld->username != $jsonData->username) {
                $appUserfindUsername = $this->userService->findByUsername($jsonData->username);
                if (!empty($appUserfindUsername)) {
                    $validator->addError('username', i18next::getTranslation('error.duplicateEmail', ['email' => $jsonData->username]));
                }
            }

            //validate duplicate email
            if ($appUserOld->email != $jsonData->email) {
                $appUserfindEmail = $this->userService->findByEmail($jsonData->email);
                if (!empty($appUserfindEmail)) {
                    $validator->addError('email', i18next::getTranslation('error.duplicateUsername', ['username' => $jsonData->email]));
                }
            }

            //update avatar
            $user->image = $appUserOld->image;

            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($user->id)) {
                    $effectRow = $this->userService->updateByObject($user, array('id' => $user->id));
                    if ($effectRow) {
                        //create user_role
                        $userRoles = isset($jsonData->userRoles) ? $jsonData->userRoles : null;
                        $this->createRoles($userRoles, $user->id);

                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    //reset user's password by admin
    public function resetPassword()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $this->pushDataToView = $this->setResponseStatus([], false, i18next::getTranslation('error.error_something_wrong'));
        $jsonData = $this->getJsonData();

        if (!empty($jsonData) && !empty($uid)) {
            $user = $this->userService->findUserDataById($jsonData->user_id);
            if ($user) {
                $newPwd = ControllerUtil::hashSha512(get_env("APP_DEFAULT_PASSWORD"));
                $randomSalt = ControllerUtil::getRadomSault();
                $effectRow = $this->userService->update([
                    'password' => ControllerUtil::genHashPassword($newPwd, $randomSalt),
                    'salt' => $randomSalt
                ], ['id' => $jsonData->user_id]);
                if ($effectRow) {
                    $this->accessTokenService->update(['revoked' => 1, 'updated_at' => DateUtils::getDateNow()], ['user' => $jsonData->user_id, 'revoked' => 0]);
                    $this->pushDataToView = $this->setResponseStatus([], true, i18next::getTranslation(('success.update_succesfull')));
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    public function changeAvatar()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $this->pushDataToView = $this->setResponseStatus([], false, i18next::getTranslation('error.error_something_wrong'));
        if (!empty($uid)) {
            $user = $this->userService->findUserDataById($uid);
            if (isset($_FILES[SystemConstant::APP_IMAGE_FILE_UPLOAD_ATT]) && is_uploaded_file($_FILES[SystemConstant::APP_IMAGE_FILE_UPLOAD_ATT]['tmp_name'])) {
                $newName = UploadUtil::getUploadFileName($uid);
                $imagName = UploadUtil::uploadProfilePic($_FILES[SystemConstant::APP_IMAGE_FILE_UPLOAD_ATT], $user->created_at, MessageUtils::getConfig('upload_image.default_width'), $newName);
                if ($imagName) {
                    //delete old image
                    if ($user->image) {
                        UploadUtil::delProfileImagefile($user->image, $user->created_at);
                    }
                    //update new image name in db
                    $effectRow = $this->userService->update([
                        'image' => $imagName
                    ], ['id' => $uid]);
                    if ($effectRow) {
                        $this->pushDataToView = $this->setResponseStatus([
                            'picture' => UploadUtil::getProfilePicApi($imagName, $user->created_at)
                        ], true);
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    public function crudDelete()
    {
        $this->pushDataToView = $this->getDefaultResponse(true);
        $idParams = FilterUtils::filterGetString(SystemConstant::ID_PARAMS);//paramiter format : idOfNo1_idOfNo2_idOfNo3_idOfNo4 ...
        $idArray = explode(SystemConstant::UNDER_SCORE, $idParams);
        if (count($idArray) > 0) {
            foreach ($idArray AS $id) {
                $entity = $this->userService->findById($id);
                if ($entity) {
                    $effectRow = $this->userService->deleteById($id);
                    if (!$effectRow) {
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
                        break;
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

}