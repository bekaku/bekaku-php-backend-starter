<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-06-02 11:09:42
 */

namespace application\controller;

use application\core\AppController;
use application\service\EdrDatabaseService;
use application\util\DataBaseConnectionUtil;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\MessageUtils;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\EdrColleage;
use application\service\EdrColleageService;
use application\validator\EdrColleageValidator;

class EdrColleageController extends AppController
{
    /**
     * @var EdrColleageService
     */
    private $edrColleageService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->edrColleageService = new EdrColleageService($this->getDbConn());

    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->edrColleageService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new EdrColleage());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->edrColleageService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->edrColleageService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $entity = new EdrColleage($jsonData, $uid, false);
            $validator = new EdrColleageValidator($entity);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                $lastInsertId = $this->edrColleageService->createByObject($entity);
                if ($lastInsertId) {
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->edrColleageService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
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
            $item = $this->edrColleageService->findById($id);
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
            $edrColleage = new EdrColleage($jsonData, $uid, true);
            $validator = new EdrColleageValidator($edrColleage);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($edrColleage->id)) {
                    $effectRow = $this->edrColleageService->updateByObject($edrColleage, array('id' => $edrColleage->id));
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
                $entity = $this->edrColleageService->findById($id);
                if ($entity) {
                    $effectRow = $this->edrColleageService->deleteById($id);
                    if (!$effectRow) {
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
                        break;
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    //test
    public function testEdrServerConection()
    {
        $id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);
        $this->pushDataToView = $this->getDefaultResponse(false);
        $item = null;
        if ($id) {
            $item = $this->edrColleageService->findById($id);
            if ($item) {


                $this->openEdrConnection($item->domain);
                $edrDatabaseService = new EdrDatabaseService($this->getEdrConnection()->getConnection());

                $configuration = $edrDatabaseService->findConfiguration();
                jsonResponse($configuration);

                $this->pushDataToView = $this->getDefaultResponse(true);
            }
        }
        jsonResponse($this->pushDataToView);
    }

}