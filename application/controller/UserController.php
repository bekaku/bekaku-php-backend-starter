<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-04 15:28:09
 */

namespace application\controller;

use application\core\AppController;
use application\service\RoleService;
use application\util\ControllerUtil;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\User;
use application\service\UserService;
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

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->userService = new UserService($this->getDbConn());
        $this->roleService = new RoleService($this->getDbConn());
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
                $validator->addError('username', 'The username ' . $jsonData->username . ' has already been taken. Please choose different username  ');
            }
            //validate duplicate email
            $appUserfindEmail = $this->userService->findByEmail($jsonData->email);
            if (!empty($appUserfindEmail)) {
                $validator->addError('email', 'The email ' . $jsonData->email . ' has already been taken. Please choose different email  ');
            }
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null));
            } else {
                $randomSalt = ControllerUtil::getRadomSault();
                $entity->password = ControllerUtil::genHashPassword($entity->password, $randomSalt);
                $entity->salt = $randomSalt;
                $entity->image = null;
                $entity->status = true;

                $lastInsertId = $this->userService->createByObject($entity);
                if ($lastInsertId) {

                    //create user_role
                    $userRoles = isset($jsonData->userRoles) ? $jsonData->userRoles : null;
                    $this->createRoles($userRoles, $lastInsertId);

                    $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.insert_succesfull')));
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
                    $validator->addError('username', 'The username ' . $jsonData->username . ' has already been taken. Please choose different username  ');
                }
            }

            //validate duplicate email
            if ($appUserOld->email != $jsonData->email) {
                $appUserfindEmail = $this->userService->findByEmail($jsonData->email);
                if (!empty($appUserfindEmail)) {
                    $validator->addError('email', 'The email ' . $jsonData->email . ' has already been taken. Please choose different email  ');
                }
            }

            $user->image = $appUserOld->image;
            $user->status = $jsonData->status;

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