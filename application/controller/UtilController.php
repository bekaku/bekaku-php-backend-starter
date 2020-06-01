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
}