<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-27 16:53:42
 */

namespace application\controller;

use application\core\AppController;
use application\service\PermissionService;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\Role;
use application\service\RoleService;
use application\validator\RoleValidator;

class RoleController extends AppController
{
    /**
     * @var RoleService
     */
    private $roleService;
    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->roleService = new RoleService($this->getDbConn());
        $this->permissionService = new PermissionService($this->getDbConn());

    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->roleService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new Role());
        $this->pushDataToView = $this->getDefaultResponse();

        $listTmp = $this->roleService->findAll($this->getRowPerPage(), $q_parameter);

        //find permission by this role
        $list = array();
        if (count($listTmp) > 0) {
            foreach ($listTmp AS $t) {
                $t->selectedPermissions = $this->permissionService->findPermissionListByRole($t->id, true);
                array_push($list, $t);
            }
        }

        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $list;
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->roleService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $entity = new Role($jsonData, $uid, false);
            $validator = new RoleValidator($entity);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                $lastInsertId = $this->roleService->createByObject($entity);
                if ($lastInsertId) {
                    $this->rolePermissionCreate($lastInsertId, $jsonData->selectedPermissions);
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->roleService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
                }
            }
        }
        jsonResponse($this->pushDataToView);

    }

    public function crudReadSingle()
    {
        $id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);
        $this->pushDataToView = $this->getDefaultResponse(false);
        $item = null;
        if ($id > 0) {
            $item = $this->roleService->findById($id);
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
            $role = new Role($jsonData, $uid, true);
            $validator = new RoleValidator($role);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($role->id)) {
                    $effectRow = $this->roleService->updateByObject($role, array('id' => $role->id));
                    if ($effectRow) {
                        $this->rolePermissionCreate($role->id, $jsonData->selectedPermissions);
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    public function crudDelete()
    {
        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation('success.delete_succesfull'));
        $idParams = FilterUtils::filterGetString(SystemConstant::ID_PARAMS);//paramiter format : idOfNo1_idOfNo2_idOfNo3_idOfNo4 ...
        $idArray = explode(SystemConstant::UNDER_SCORE, $idParams);
        if (count($idArray) > 0) {
            foreach ($idArray AS $id) {
                $entity = $this->roleService->findById($id);
                if ($entity) {

                    //delete old permission from role_permission
                    $this->permissionService->deleteRolePermissionByRole($id);

                    $effectRow = $this->roleService->deleteById($id);
                    if (!$effectRow) {
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
                        break;
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    //role permission
    public function rolePermissionCreate($roleId, $permissions = array())
    {
        if (!$roleId && count($permissions) == 0) {
            return false;
        }

        //delete old permission from role_permission
        $roleId = (int)$roleId;
        $this->permissionService->deleteRolePermissionByRole($roleId);

        //insert new permission
        foreach ($permissions AS $permissionId) {
            $this->permissionService->createRolePermission([
                'permission' => $permissionId,
                'role' => $roleId,
            ]);
        }

        return true;
    }

}