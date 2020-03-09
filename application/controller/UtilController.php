<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 29/12/2015
 * Time: 10:30 AM
 */

namespace application\controller;

use application\core\AppController as BaseController;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\SystemConstant;

class UtilController extends BaseController
{

    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
        $this->isAuthRequired = false;
    }
    public function __destruct(){

    }
    public function jsonGetServerDateAndTime()
    {
        //date_default_timezone_set('Asia/Bangkok');
//        $nowDate = DateUtils::getDateNow(true);
//      $returnArr['current_time']=strtotime($nowDate);
        $returnArr['current_date']=DateUtils::getDateNow(true);
        echo json_encode($returnArr);
    }
    public function changeSystemLocale(){
        $lang = FilterUtils::filterPostString('_lang');
        if(!AppUtil::isEmpty($lang)){

            $currentLocale = ControllerUtil::getCurrentLocale();
            if($currentLocale==$lang){
                $this->pushDataToView['status'] = '0';
            }else{
                //set to cookie
                ControllerUtil::setCookieByName(SystemConstant::COOKIE_LOCALE_ATT, $lang);
                $this->pushDataToView['status'] = '1';
            }

        }else{
            $this->pushDataToView['status'] = '0';
        }

        $this->pushDataToView['lang'] = $lang;

        echo json_encode($this->pushDataToView);
    }
    public function jsonGetUniqeToken(){

        $this->requiredJsonHeadersGet();
        $this->pushDataToView['uniqe_token_cookie'] = ControllerUtil::getUniqeTokenCookie();
        echo json_encode($this->pushDataToView);
    }

    /*
     * TEST METHOD
     */
    public function jsonTestGet()
    {

        $appUserId = FilterUtils::filterGetString('app_user_id');

        $returnData['json_test'] = "jsonTest GET get parameter from client==>".$appUserId;

        print_r($returnData);
    }
    public function jsonTestPost()
    {
        $appUserId = FilterUtils::filterPostString('app_user_id');
        $returnData['json_test'] = "jsonTest POST post paramiter from client==>".$appUserId;

        print_r($returnData);
    }

    public function jsonTest()
    {

    }
}