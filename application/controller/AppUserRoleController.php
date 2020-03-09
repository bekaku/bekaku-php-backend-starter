<?php
namespace application\controller;

use application\core\AppController;
use application\service\AppPermissionService;
use application\service\AuthenService;
use application\util\FilterUtils;
use application\util\ControllerUtil;
use application\util\AppUtil;
use application\util\i18next;
use application\util\MessageUtils;
use application\util\SecurityUtil;
use application\util\SystemConstant;
use application\util\UploadUtil;

use application\model\AppUserRole;
use application\service\AppUserRoleService ;
use application\validator\AppUserRoleValidator ;
class AppUserRoleController extends  AppController
{
    private $appUserRoleService;
    private $appPermissionService;
    private $authenService;
    public function __construct($databaseConnection)
    {

        $this->setDbConn($databaseConnection);
        $this->appUserRoleService = new AppUserRoleService($this->getDbConn());
        $this->appPermissionService = new AppPermissionService($this->getDbConn());
        $this->authenService = new AuthenService($this->getDbConn());

    }
    public function __destruct()
    {
        unset($this->appUserRoleService);
        unset($this->appPermissionService);
        unset($this->authenService);
    }
    public function crudList()
    {

        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) >0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        if($perPage>0){
            $this->setRowPerPage($perPage);
        }
        $q_parameter = $this->initSearchParam(new AppUserRole());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->appUserRoleService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->appUserRoleService->getTotalPaging();
        $this->jsonResponse($this->pushDataToView);
    }
    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();

        $jsonData = $this->getJsonData(false);

        $this->pushDataToView = $this->getDefaultResponse();
        $appUserRole = new AppUserRole();
        $appUserRole->populatePostDataRestApi($jsonData);


        $validator = new AppUserRoleValidator($appUserRole);
        $errors = $validator->getValidationErrors();
        if($errors){
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, $errors);
        }else {
            $appUserRole=$this->initBaseCreateDataRestApi($appUserRole, $uid);

            $lastInsertId = $this->appUserRoleService->createByObject($appUserRole);
            if($lastInsertId){
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.insert_succesfull')));
            }else{
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
            }
        }

        $this->jsonResponse($this->pushDataToView);
    }
    public function crudEdit()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();

        $id = FilterUtils::validateGetInt(ControllerUtil::encodeParamId(AppUserRole::$tableName));
        if(AppUtil::isEmpty($id)) {
            ControllerUtil::f404Static();
        }
        $appUserRoleOld = $this->appUserRoleService->findById($id);
        if(!$appUserRoleOld){
            ControllerUtil::f404Static();
        }

        $this->pushDataToView = $this->getDefaultResponse();

        $appUserRole = new AppUserRole();
        $appUserRole->populatePostDataRestApi($this->getJsonData(false));
        $appUserRole->setId($id);

        $validator = new AppUserRoleValidator($appUserRole);
        $errors = $validator->getValidationErrors();
        if($errors){
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, $errors);
        }else{
            $appUserRole=$this->initBaseUpdateDataRestApi($appUserRole, $uid);

            $effectRow = $this->appUserRoleService->updateByObject($appUserRole, array('id'=> $appUserRole->getId()));
            if($effectRow){
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
            }else{
                $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
            }
        }
        $this->jsonResponse($this->pushDataToView);
    }
    public function crudDelete()
    {
        $id = FilterUtils::validateGetInt(ControllerUtil::encodeParamId(AppUserRole::$tableName));
        if(AppUtil::isEmpty($id)) {
            ControllerUtil::f404Static();
        }
        $appUserRole = $this->appUserRoleService->findById($id);
        if(!$appUserRole){
            ControllerUtil::f404Static();
        }

        //then delete permission
        $effectRow = $this->appUserRoleService->deleteById($id);
        if(!$effectRow){
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
        }else{
            $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.delete_succesfull')));
        }

        $this->jsonResponse($this->pushDataToView);
    }

}