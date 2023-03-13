<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
/**
 * Created by Bekaku Php Back End System.
 * Date: 2023-03-09 15:13:42
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\Major;
use application\service\MajorService ;
class MajorController extends  AppController
{
    /**
    * @var MajorService
    */
    private $majorService;
    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->majorService = new MajorService($this->getDbConn());

    }
    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->majorService);
    }
    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new Major());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->majorService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->majorService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }
    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if(!empty($jsonData) && !empty($uid)) {
           $entity = new Major($jsonData, $uid, false);
               $lastInsertId = $this->majorService->createByObject($entity);
               if ($lastInsertId) {
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->majorService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
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
            $item = $this->majorService->findById($id);
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
           $major = new Major($jsonData, $uid, true);
                if (isset($major->id)) {
                   $effectRow = $this->majorService->updateByObject($major, array('id' => $major->id));
                   if ($effectRow) {
                       $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
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
                $entity = $this->majorService->findById($id);
                if ($entity) {
                    $effectRow = $this->majorService->deleteById($id);
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