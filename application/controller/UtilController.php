<?php

/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 29/12/2015
 * Time: 10:30 AM
 */

namespace application\controller;

use application\core\AppController;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\UploadUtil;
use application\util\i18next;
use application\util\SecurityUtil;

class UtilController extends AppController
{
    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
    }
    public function __destruct()
    {
    }

    public function jsonGetServerDateAndTime()
    {
        jsonResponse(['currentDatetime' => DateUtils::getDateNow(true)]);
    }

    public function jsonGetUniqeToken()
    {
        jsonResponse(['uniqeTokenCookie' => ControllerUtil::getUniqeTokenCookie()]);
    }
    public function getSiteMetadata()
    {
        jsonResponse(AppUtil::getSiteMetaData(FilterUtils::filterGetString('uri')));
    }

    public function uploadImageApi()
    {
        //get user's id from JWT Token who change student's image.
        $uid = SecurityUtil::getAppuserIdFromJwtPayload();
        //check is user upload file or not
        if (isset($_FILES['fileName']) && is_uploaded_file($_FILES['fileName']['tmp_name'])) {
            //generate random unique name for this image
            $newName = UploadUtil::getUploadFileName($uid);
            //upload process if upload cuccess it will return image name
            $imagName = UploadUtil::uploadImgFiles($_FILES['fileName'], null, 0, $newName);
            if ($imagName) {
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
}
