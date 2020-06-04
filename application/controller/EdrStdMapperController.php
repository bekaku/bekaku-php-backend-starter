<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-06-01 16:49:32
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\EdrStdMapper;
use application\service\EdrStdMapperService;
use application\validator\EdrStdMapperValidator;

class EdrStdMapperController extends AppController
{
    /**
     * @var EdrStdMapperService
     */
    private $edrStdMapperService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->edrStdMapperService = new EdrStdMapperService($this->getDbConn());

    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->edrStdMapperService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new EdrStdMapper());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->edrStdMapperService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->edrStdMapperService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $entity = new EdrStdMapper($jsonData, $uid, false);
            $validator = new EdrStdMapperValidator($entity);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                $lastInsertId = $this->edrStdMapperService->createByObject($entity);
                if ($lastInsertId) {
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->edrStdMapperService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
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
            $item = $this->edrStdMapperService->findById($id);
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
            $edrStdMapper = new EdrStdMapper($jsonData, $uid, true);
            $validator = new EdrStdMapperValidator($edrStdMapper);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($edrStdMapper->id)) {
                    $effectRow = $this->edrStdMapperService->updateByObject($edrStdMapper, array('id' => $edrStdMapper->id));
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
                $entity = $this->edrStdMapperService->findById($id);
                if ($entity) {
                    $effectRow = $this->edrStdMapperService->deleteById($id);
                    if (!$effectRow) {
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
                        break;
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    //api
    public function edrStdMapperApi()
    {
        $func = FilterUtils::filterGetString('functionName');
        $stdCode = FilterUtils::filterGetString('stdCode');

        $this->pushDataToView = $this->getDefaultResponse(false);
        $item = null;
        if ($func && $stdCode) {
            $item = $this->edrStdMapperService->findByFunctionAndStd($func, $stdCode);
            if ($item) {
                $this->pushDataToView = $this->getDefaultResponse(true);
            }
        }
        jsonResponse(['data' => $item]);
    }

}