<?php

namespace application\controller;

use application\core\AppController as BaseController;
use application\model\AppPermission;
use application\service\AppPermissionService as AppPermissionService;
use application\service\AuthenService;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\FilterUtils as FilterUtil;
use application\util\i18next;
use application\util\SecurityUtil;
use application\util\SystemConstant;
use application\validator\AppPermissionValidator;

class AppPermissionController extends BaseController
{
    private $appPermissionService;
    private $authenService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->appPermissionService = new AppPermissionService($this->getDbConn());
        $this->authenService = new AuthenService($this->getDbConn());

    }

    public function __destruct()
    {
        unset($this->appPermissionService);
        unset($this->authenService);
    }

    public function crudList()
    {

        $perPage = FilterUtil::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtil::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new AppPermission());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->appPermissionService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->appPermissionService->getTotalPaging();
        $this->jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {

        $uid = SecurityUtil::getAppuserIdFromJwtPayload();

        $jsonData = $this->getJsonData(false);

        $this->pushDataToView = $this->getDefaultResponse();
        $appPermission = new AppPermission();
        $appPermission->populatePostDataRestApi($jsonData);


        $validator = new AppPermissionValidator($appPermission);
        $errors = $validator->getValidationErrors();
        if ($errors) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, $errors);
        } else {

            $appPermission->setCreatedUser($uid);
            $appPermission->setUpdatedUser($uid);

            $appPermission->setCrudTable($appPermission->getName());
            $permissionName = $appPermission->getName();
            $permissionDescription = $appPermission->getDescription();
            $isCrudPermission = false;

            $cruds = $jsonData->cruds;
            if ($cruds) {
                foreach ($cruds AS $crud) {

                    if ($crud == 'list') {
                        $appPermission->setName($permissionName . "_list");
                        $appPermission->setDescription($permissionDescription . ' (' . i18next::getTranslation('base.list') . ')');
                        $listPermissionInsertId = $this->appPermissionService->createByObject($appPermission);

                        //create all permission for dev
                        $dataCrudList['role'] = 1;
                        $dataCrudList['permission'] = $listPermissionInsertId;
                        $dataCrudList = $this->initBaseCreateDataByUid($dataCrudList, $uid);
                        $this->appPermissionService->createPermissionRole($dataCrudList);
                        $isCrudPermission = true;
                    }
                    if ($crud == 'add') {
                        $appPermission->setName($permissionName . "_add");
                        $appPermission->setDescription($permissionDescription . ' (' . i18next::getTranslation('base.add_new') . ')');
                        $addPermissionInsertId = $this->appPermissionService->createByObject($appPermission);
                        //create all permission for dev
                        $dataCrudAdd['role'] = 1;
                        $dataCrudAdd['permission'] = $addPermissionInsertId;
                        $dataCrudAdd = $this->initBaseCreateDataByUid($dataCrudAdd, $uid);
                        $this->appPermissionService->createPermissionRole($dataCrudAdd);
                        $isCrudPermission = true;
                    }
                    if ($crud == 'edit') {
                        $appPermission->setName($permissionName . "_edit");
                        $appPermission->setDescription($permissionDescription . ' (' . i18next::getTranslation('base.edit') . ')');
                        $editPermissionInsertId = $this->appPermissionService->createByObject($appPermission);
                        //create all permission for dev
                        $dataCrudEdit['role'] = 1;
                        $dataCrudEdit['permission'] = $editPermissionInsertId;
                        $dataCrudEdit = $this->initBaseCreateDataByUid($dataCrudEdit, $uid);
                        $this->appPermissionService->createPermissionRole($dataCrudEdit);
                        $isCrudPermission = true;
                    }
                    if ($crud == 'delete') {
                        $appPermission->setName($permissionName . "_delete");
                        $appPermission->setDescription($permissionDescription . ' (' . i18next::getTranslation('base.delete') . ')');
                        $deletePermissionInsertId = $this->appPermissionService->createByObject($appPermission);
                        //create all permission for dev
                        $dataCrudDelete['role'] = 1;
                        $dataCrudDelete['permission'] = $deletePermissionInsertId;
                        $dataCrudDelete = $this->initBaseCreateDataByUid($dataCrudDelete, $uid);
                        $this->appPermissionService->createPermissionRole($dataCrudDelete);
                        $isCrudPermission = true;
                    }
                    if ($crud == 'view') {
                        $appPermission->setName($permissionName . "_view");
                        $appPermission->setDescription($permissionDescription . ' (' . i18next::getTranslation('base.view') . ')');
                        $viewPermissionInsertId = $this->appPermissionService->createByObject($appPermission);
                        //create all permission for dev
                        $dataCrudView['role'] = 1;
                        $dataCrudView['permission'] = $viewPermissionInsertId;
                        $dataCrudView = $this->initBaseCreateDataByUid($dataCrudView, $uid);
                        $this->appPermissionService->createPermissionRole($dataCrudView);
                        $isCrudPermission = true;
                    }
                }
            }

            if (!$isCrudPermission) {
                $permissionInsertId = $this->appPermissionService->createByObject($appPermission);
                //create all permission for dev
                $dataPermission['role'] = 1;
                $dataPermission['permission'] = $permissionInsertId;
                $dataPermission = $this->initBaseCreateDataByUid($dataPermission, $uid);
                $this->appPermissionService->createPermissionRole($dataPermission);
            }

            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, 'permission created success');
        }
        $this->jsonResponse($this->pushDataToView);
    }

    public function crudEdit()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $id = FilterUtil::validateGetInt(ControllerUtil::encodeParamId(AppPermission::$tableName));

        if (AppUtil::isEmpty($id)) {
            ControllerUtil::f404Static();
        }

        $this->pushDataToView = $this->getDefaultResponse();


        $appPermission = new AppPermission();
        $appPermission->populatePostDataRestApi($this->getJsonData(false));
        $appPermission->setId($id);

        $validator = new AppPermissionValidator($appPermission);
        $errors = $validator->getValidationErrors();
        if ($errors) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, $errors);
        } else {
            $appPermission = $this->initBaseUpdateDataRestApi($appPermission, $uid);

            $effectRow = $this->appPermissionService->updateByObject($appPermission, array('id' => $appPermission->getId()));
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
        $this->pushDataToView = $this->getDefaultResponse();
        $id = FilterUtil::validateGetInt(ControllerUtil::encodeParamId(AppPermission::$tableName));
        if (AppUtil::isEmpty($id)) {
            ControllerUtil::f404Static();
        }
        $appPermission = $this->appPermissionService->findById($id);
        if (!$appPermission) {
            ControllerUtil::f404Static();
        }

        //delete in permission role frist
        $this->appPermissionService->deletePermissionRoleByPermission($id);
        //then delete permission
        $effectRow = $this->appPermissionService->deleteById($id);
        if (!$effectRow) {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
        } else {
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.delete_succesfull')));
        }

        $this->jsonResponse($this->pushDataToView);
    }
}