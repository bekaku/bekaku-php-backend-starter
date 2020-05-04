<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-04 15:19:58
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\ApiClientIp;
use application\service\ApiClientIpService ;
use application\validator\ApiClientIpValidator ;
class ApiClientIpController extends  AppController
{
    /**
    * @var ApiClientIpService
    */
    private $apiClientIpService;
    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->apiClientIpService = new ApiClientIpService($this->getDbConn());

    }
    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->apiClientIpService);
    }
    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new ApiClientIp());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->apiClientIpService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->apiClientIpService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }
    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if(!empty($jsonData) && !empty($uid)) {
           $entity = new ApiClientIp($jsonData, $uid, false);
           $validator = new ApiClientIpValidator($entity);
           if ($validator->getValidationErrors()) {
               jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
           } else {
               $lastInsertId = $this->apiClientIpService->createByObject($entity);
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
            $item = $this->apiClientIpService->findById($id);
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
           $apiClientIp = new ApiClientIp($jsonData, $uid, true);
           $validator = new ApiClientIpValidator($apiClientIp);
           if ($validator->getValidationErrors()) {
               jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
           } else {
                if (isset($apiClientIp->id)) {
                   $effectRow = $this->apiClientIpService->updateByObject($apiClientIp, array('id' => $apiClientIp->id));
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
                $entity = $this->apiClientIpService->findById($id);
                if ($entity) {
                    $effectRow = $this->apiClientIpService->deleteById($id);
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