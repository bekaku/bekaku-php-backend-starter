<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-04 15:12:07
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\Role;
use application\service\RoleService ;
use application\validator\RoleValidator ;
class RoleController extends  AppController
{
    /**
    * @var RoleService
    */
    private $roleService;
    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->roleService = new RoleService($this->getDbConn());

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
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->roleService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->roleService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }
    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if(!empty($jsonData) && !empty($uid)) {
           $entity = new Role($jsonData, $uid, false);
           $validator = new RoleValidator($entity);
           if ($validator->getValidationErrors()) {
               jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
           } else {
               $lastInsertId = $this->roleService->createByObject($entity);
               if ($lastInsertId) {
                   $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.insert_succesfull')));
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
		
        if(!empty($jsonData) && !empty($uid)) {
           $role = new Role($jsonData, $uid, true);
           $validator = new RoleValidator($role);
           if ($validator->getValidationErrors()) {
               jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
           } else {
                if (isset($role->id)) {
                   $effectRow = $this->roleService->updateByObject($role, array('id' => $role->id));
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
        $this->pushDataToView = $this->getDefaultResponse(true);
        $idParams = FilterUtils::filterGetString(SystemConstant::ID_PARAMS);//paramiter format : idOfNo1_idOfNo2_idOfNo3_idOfNo4 ...
        $idArray = explode(SystemConstant::UNDER_SCORE, $idParams);
        if (count($idArray) > 0) {
            foreach ($idArray AS $id) {
                $entity = $this->roleService->findById($id);
                if ($entity) {
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

}