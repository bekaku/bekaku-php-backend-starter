<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/

/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-27 14:31:18
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\Permission;
use application\service\PermissionService;
use application\validator\PermissionValidator;

class PermissionController extends AppController
{
    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->permissionService = new PermissionService($this->getDbConn());

    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->permissionService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new Permission());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->permissionService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->permissionService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }
    public function findAllByPaging()
    {
        $page = FilterUtils::filterGetInt(SystemConstant::PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PAGE_ATT) : 0;
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        jsonResponse($this->permissionService->findAllByPaging($page, $perPage));
    }

    public function permissionsCrudtbl()
    {

        $crudTableList = $this->permissionService->findAllCrudTable();
        $items = [];
        $groupId = 1;
        if (count($crudTableList) > 0) {
            foreach ($crudTableList AS $tbl) {
                array_push($items, [
                    'id' => 'parent#'.$groupId,
                    'description' => i18next::getTranslation("model.$tbl->crud_table.$tbl->crud_table"),
                    'children' => $this->permissionService->findAllByCrudTbl($tbl->crud_table)
                ]);
                $groupId++;
            }
        }
        //other group
        $otherList = $this->permissionService->findAllByEmptyCrudTbl();
        if($otherList && count($otherList)>0){
            array_push($items, [
                'id' => 'parent#'.$groupId,
                'description' => i18next::getTranslation("base.other"),
                'children' => $otherList
            ]);
        }


        jsonResponse($items);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $entity = new Permission($jsonData, $uid, false);
            $validator = new PermissionValidator($entity);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {

                $lastInsertId = $this->permissionService->createByObject($entity);
                if ($lastInsertId) {
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->permissionService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
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
            $item = $this->permissionService->findById($id);
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
            $permission = new Permission($jsonData, $uid, true);
            $validator = new PermissionValidator($permission);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($permission->id)) {
                    $effectRow = $this->permissionService->updateByObject($permission, array('id' => $permission->id));
                    if ($effectRow) {
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
                $entity = $this->permissionService->findById($id);
                if ($entity) {
                    $effectRow = $this->permissionService->deleteById($id);
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