<?php

/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
/**
 * Created by Bekaku Php Back End System.
 * Date: 2023-03-09 13:11:10
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;
use application\util\SecurityUtil;

use application\model\Student;
use application\service\StudentService;

class StudentController extends  AppController
{
    /**
     * @var StudentService
     */
    private $studentService;


    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->studentService = new StudentService($this->getDbConn());
    }
    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->studentService);
    }
    public function crudList()
    {
        $perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;
        $this->setRowPerPage($perPage);
        $q_parameter = $this->initSearchParam(new Student());

        $this->pushDataToView = $this->getDefaultResponse();
        $this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $this->studentService->findAll($this->getRowPerPage(), $q_parameter);
        $this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $this->studentService->getTotalPaging();
        jsonResponse($this->pushDataToView);
    }

    public function crudAdd()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->validateData($jsonData);
        $this->pushDataToView = $this->getDefaultResponse(false);
        if (!empty($jsonData) && !empty($uid)) {
            $entity = new Student($jsonData, $uid, false);
            $lastInsertId = $this->studentService->createByObject($entity);
            if ($lastInsertId) {
                $this->pushDataToView = $this->setResponseStatus([SystemConstant::ENTITY_ATT => $this->studentService->findById($lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));
            }
        }
        jsonResponse($this->pushDataToView);
    }
    private function validateData($jsonData)
    {

        if (empty($jsonData->std_code)) {
            jsonResponse([
                'error' => i18next::getTranslation('error.emptyError', ['data' => 'student code'])
            ]);
        }
        $stdExist = $this->studentService->findByCode($jsonData->std_code);
        if (!empty($stdExist)) {
            jsonResponse([
                'error' => i18next::getTranslation('error.duplicateError', ['data' => 'student code'])
            ]);
        }
    }

    public function crudAddV2()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->validateData($jsonData);
        if (!empty($jsonData) && !empty($uid)) {

            $lastInsertId =  $this->studentService->createByArray([
                'std_code' => $jsonData->std_code,
                'name' => $jsonData->name,
                'surname' => $jsonData->surname,
                'birth_date' => $jsonData->birth_date,
                'major_id' => $jsonData->major_id,
            ]);

            jsonResponse([
                'insertId' => $lastInsertId
            ]);
        }

        jsonResponse([
            'error' => 'Action fail!!!!'
        ]);
    }


    public function crudReadSingle()
    {
        $id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);
        $this->pushDataToView = $this->getDefaultResponse(false);
        $item = null;
        if ($id > 0) {
            $item = $this->studentService->findById($id);
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
            $student = new Student($jsonData, $uid, true);
            if (isset($student->id)) {
                $effectRow = $this->studentService->updateByObject($student, array('id' => $student->id));
                if ($effectRow) {
                    $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));
                }
            }
        }
        jsonResponse($this->pushDataToView);
    }
    public function crudEditV2()
    {
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $jsonData = $this->getJsonData(false);
        $this->pushDataToView = $this->getDefaultResponse(false);

        if (!empty($jsonData) && !empty($uid)) {
            $this->studentService->update(
                [
                    'name' => $jsonData->name,
                    'surname' => $jsonData->surname,
                ],
                ['id' => $jsonData->id]
            );
        }
        jsonResponse($this->pushDataToView);
    }



    public function crudDelete()
    {
        $this->pushDataToView = $this->setResponseStatus($this->pushDataToView, true, i18next::getTranslation('success.delete_succesfull'));
        $idParams = FilterUtils::filterGetString(SystemConstant::ID_PARAMS); //paramiter format : idOfNo1_idOfNo2_idOfNo3_idOfNo4 ...
        $idArray = explode(SystemConstant::UNDER_SCORE, $idParams);
        if (count($idArray) > 0) {
            foreach ($idArray as $id) {
                $entity = $this->studentService->findById($id);
                if ($entity) {
                    $effectRow = $this->studentService->deleteById($id);
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
