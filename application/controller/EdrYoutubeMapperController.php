<?php
/**
 * Created by Bekaku Php Back End System.
 * Date: 2020-05-18 17:14:22
 */

namespace application\controller;

use application\core\AppController;
use application\util\AppUtil;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\EdrYoutubeMapper;
use application\service\EdrYoutubeMapperService;
use application\validator\EdrYoutubeMapperValidator;

class EdrYoutubeMapperController extends AppController
{
    /**
     * @var EdrYoutubeMapperService
     */
    private $edrYoutubeMapperService;

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->edrYoutubeMapperService = new EdrYoutubeMapperService($this->getDbConn());

    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->edrYoutubeMapperService);
    }

    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new EdrYoutubeMapper());
//        $q_parameter[SystemConstant::SORT_MODE_ATT] = 'asc';
//        $q_parameter[SystemConstant::SORT_FIELD_ATT] = 'id';
//        jsonResponse($q_parameter);

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->edrYoutubeMapperService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->edrYoutubeMapperService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);



        if (!empty($jsonData) && !empty($uid)) {
            $entity = new EdrYoutubeMapper($jsonData, $uid, false);
            $validator = new EdrYoutubeMapperValidator($entity);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if ($jsonData->youtube_link) {
                    $siteMeta = AppUtil::getSiteMetaData($jsonData->youtube_link);
                    if ($siteMeta) {
                        $entity->metadata = json_encode($siteMeta);
                    }
                }

                $lastInsertId = $this->edrYoutubeMapperService->createByObject($entity);
                if ($lastInsertId) {

                    $edrList = $jsonData->edrList;
                    if (count($edrList) > 0) {
                        foreach ($edrList AS $link) {
                            $this->edrYoutubeMapperService->createEdrLink([
                                'edr_youtube_mapper' => $lastInsertId,
                                'edr_link' => $link->edr_link,
                            ]);
                        }
                    }

                    $this->pushDataToView = $this->setResponseStatus(['item' => $this->edrYoutubeMapperService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
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
            $item = $this->edrYoutubeMapperService->findById($id);
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
            $edrYoutubeMapper = new EdrYoutubeMapper($jsonData, $uid, true);
            $validator = new EdrYoutubeMapperValidator($edrYoutubeMapper);
            if ($validator->getValidationErrors()) {
                jsonResponse($this->setResponseStatus($validator->getValidationErrors(), false, null), 400);
            } else {
                if (isset($edrYoutubeMapper->id)) {
                    if ($jsonData->youtube_link) {
                        $siteMeta = AppUtil::getSiteMetaData($jsonData->youtube_link);
                        if ($siteMeta) {
                            $edrYoutubeMapper->metadata = json_encode($siteMeta);
                        }
                    }
                    $effectRow = $this->edrYoutubeMapperService->updateByObject($edrYoutubeMapper, array('id' => $edrYoutubeMapper->id));
                    if ($effectRow) {

                        $edrList = $jsonData->edrList;
                        if (count($edrList) > 0) {
                            $this->edrYoutubeMapperService->deleteEdrLinkByYoutubeId($jsonData->id);

                            foreach ($edrList AS $link) {
                                $this->edrYoutubeMapperService->createEdrLink([
                                    'edr_youtube_mapper' => $jsonData->id,
                                    'edr_link' => $link->edr_link,
                                ]);
                            }
                        }

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
                $entity = $this->edrYoutubeMapperService->findById($id);
                if ($entity) {
                    $effectRow = $this->edrYoutubeMapperService->deleteById($id);
                    if (!$effectRow) {
                        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));
                        break;
                    }
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }

    //API
    public function checkYtLink()
    {
        $edrUrl = FilterUtils::filterGetString('uri');
        if (!$edrUrl) {
            jsonResponse(null);
        }
        jsonResponse($this->edrYoutubeMapperService->checkYoutubelink($edrUrl));
    }

}