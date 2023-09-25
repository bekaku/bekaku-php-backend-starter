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
use application\service\StudentImageService;
use application\util\DateUtils;
use application\util\UploadUtil;

class StudentController extends AppController
{
    /**
     * @var StudentService
     */
    private $studentService;

    private $studentImageService;


    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->studentService = new StudentService($this->getDbConn());
        $this->studentImageService = new StudentImageService($this->getDbConn());
    }

    public function __destruct()
    {
        $this->setDbConn(null);
        unset($this->studentService);
        unset($this->studentImageService);
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

                // if upload multiple file 
                if (isset($jsonData->imageNameList) && count($jsonData->imageNameList) > 0) {
                    foreach ($jsonData->imageNameList as $imgName) {
                    }
                }


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

            $lastInsertId = $this->studentService->createByArray([
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

                // if this update has new image upload then delete old image before update this data
                if ($jsonData->haveNewImage) {
                    $studentOld = $this->studentService->findById($student->id);
                    if (!empty($studentOld)) {
                        //delete old student's image before upload new one
                        if (!empty($studentOld->image_name)) {
                            UploadUtil::delImgfileFromYearMonthFolder($studentOld->image_name, null);
                        }
                    }
                }


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


    public function studentUploadImage()
    {
        //get studentId
        $studentId = $_POST["studentId"];
        //get user's id from JWT Token who change student's image.
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        //check is user upload file or not
        if (isset($_FILES['fileName']) && is_uploaded_file($_FILES['fileName']['tmp_name'])) {
            // get student data by id
            $student = $this->studentService->findById($studentId);
            if (!empty($student)) {
                //delete old student's image before upload new one
                if (!empty($student->image_name)) {
                    UploadUtil::delImgfileFromYearMonthFolder($student->image_name, null);
                }
            }
            //generate random unique name for this image
            $newName = UploadUtil::getUploadFileName($uid);
            //upload process if upload cuccess it will return image name
            $imagName = UploadUtil::uploadImgFiles($_FILES['fileName'], null, 0, $newName);
            if ($imagName) {
                //update this student's image_name in db by id
                $this->studentService->update([
                    'image_name' => $imagName
                ], ['id' => $studentId]);
                //return image name to frontend
                jsonResponse([
                    'imageName' => $imagName,
                ]);
            }
        }
        //return error message if upload fail
        jsonResponse([
            'error' => i18next::getTranslation('error.oops'),
        ]);
    }

    public function studentUploadMultiImage()
    {
        //get studentId
        $studentId = $_POST["studentId"];
        $totalFiles = $_POST["totalFile"];
        //get user's id from JWT Token who change student's image.
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        $imgList = array();
        if (!empty($uid) && !empty($studentId) && $totalFiles > 0) {

            for ($i = 0; $i < $totalFiles; $i++) {

                $fileUploadName = 'fileName_' . $i;
                if (is_uploaded_file($_FILES[$fileUploadName]['tmp_name'])) {
                    //generate random unique name for this image
                    $newName = UploadUtil::getUploadFileName($studentId . '_' . $uid);
                    //upload process if upload cuccess it will return image name
                    $imagName = UploadUtil::uploadImgFiles($_FILES[$fileUploadName], null, 0, $newName);
                    if ($imagName) {
                        // insert this image to student_image table
                        $this->studentImageService->createByArray(
                            [
                                'student_id' => $studentId,
                                'image_name' => $imagName,
                                'upload_user' => $uid,
                                'upload_date' => DateUtils::getDateNow()
                            ]
                        );

                        //push new image to list
                        array_push($imgList, $imagName);
                    }
                }
            }
            //return list of upload images
            jsonResponse([
                'images' => $imgList
            ]);
        }
        //return error message if upload fail
        jsonResponse([
            'error' => i18next::getTranslation('error.oops'),
        ]);
    }

    public function testChartData()
    {
        $series = array();
        $cates = array("Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct");
        array_push($series, [
            'name' => 'chart 1',
            'data' => array(44, 55, 57, 56, 61, 58, 63, 60, 66)
        ]);

        jsonResponse([
            'cates' => $cates,
            'series' => $series
        ]);
    }
}
