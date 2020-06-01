<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-27 15:02:33
 */

namespace application\controller;

use application\core\AppController;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\ApiClient;
use application\service\ApiClientService;
use application\validator\ApiClientValidator;

class ApiClientController extends AppController
{
    /**
     * @var ApiClientService
     */
    private $apiClientService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->apiClientService = new ApiClientService($this->getDbConn());

    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->apiClientService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new ApiClient());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->apiClientService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->apiClientService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $entity = new ApiClient($jsonData, $uid, false);
            //generate token
            $entity->api_token = ControllerUtil::hashSha512(ControllerUtil::getRadomSault().DateUtils::getTimeNow());

            $validator = new ApiClientValidator($entity);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                $lastInsertId = $this->apiClientService->createByObject($entity);
                if ($lastInsertId) {
                    $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->apiClientService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
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
            $item = $this->apiClientService->findById($id);
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
            $apiClient = new ApiClient($jsonData, $uid, true);
            $validator = new ApiClientValidator($apiClient);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($apiClient->id)) {
                    $effectRow = $this->apiClientService->updateByObject($apiClient, array('id' => $apiClient->id));
                    if ($effectRow) {
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }
    public function refreshToken()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if ($id > 0) {
            $apiClient = $this->apiClientService->findById($id);
            if ($apiClient) {
                //generate token
                $apiClient->api_token = ControllerUtil::hashSha512(ControllerUtil::getRadomSault().DateUtils::getTimeNow());
                $apiClient->updated_user=$uid;
                $apiClient->updated_at = DateUtils::getDateNow(true);

                $effectRow = $this->apiClientService->update($apiClient, array('id' => $apiClient->id));
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
                $entity = $this->apiClientService->findById($id);
                if ($entity) {
                    $effectRow = $this->apiClientService->deleteById($id);
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