<?php namespace application\core;

use application\service\AuthenService;
use application\util\AppUtil;
use application\util\ControllerUtil as ControllerUtils;
use application\util\ControllerUtil;
use application\util\DataBaseConnectionUtil;
use application\util\DateUtils as DateUtil;
use application\util\FilterUtils as FilterUtil;
use application\util\AppUtil as AppUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\MessageUtils;
use application\util\SystemConstant;

class AppController
{

    public static $errorCustomMessage;
    private $dbConn;

    public $headerContentType = "Content-Type: application/json; charset=utf-8";//default
    public $isAuthRequired = true;
    public $validateErrors;
    public $modelObject = array();
    public $pushDataToView = array();

    public $styleCssAditional = array();
    public $scriptJsAditional = array();

    public $isGeneratorHeader = true;
    public $isGeneratorFooter = true;
    public $isUseAppTemplate = true;

    private $rowPerPage = 10;
    public $theme = "default";

    public $metaTitle;
    public $metaDescription;
    public $metaKeyword;
    public $reqPlatform = "windows";

    //history connection
    public $historyConnection;

    public function loadView($viewPath = null, $_V_DATA_TO_VIEW = array())
    {

        if ($this->checkView($viewPath)) {
            include(__SITE_PATH . "/application/views/" . $this->theme . "/" . $viewPath . ".php");
        } else {
            ControllerUtils::f404Static($this->theme);
        }
    }

    //history connection
    public function openHistoryConnection()
    {
        $historyDbConf = MessageUtils::getConfig('mysql_wim_history');
        $this->historyConnection = new DataBaseConnectionUtil($historyDbConf);
        $this->historyConnection->openMysqlConnection();
    }

    /**
     * @return mixed
     */
    public function getHistoryConnection()
    {
        return $this->historyConnection;
    }
    //end history connection


    /**
     * @return mixed
     */
    public function getDbConn()
    {
        return $this->dbConn;
    }

    /**
     * @param $dbConn
     */
    public function setDbConn($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    /**
     * @return int
     */
    public function getRowPerPage()
    {
        return $this->rowPerPage;
    }

    /**
     * @param int $rowPerPage
     */
    public function setRowPerPage($rowPerPage)
    {
        $this->rowPerPage = $rowPerPage;
    }

    public function initBaseCreateData($data_array = array())
    {
        $data_array['created_user'] = ControllerUtils::getUserIdSession();
        $data_array['created_date'] = DateUtil::getDateNow();
        $data_array['updated_user'] = ControllerUtils::getUserIdSession();
        $data_array['updated_date'] = DateUtil::getDateNow();
        return $data_array;
    }

    public function initBaseCreateDataByUid($data_array, $uid)
    {
        $data_array['created_user'] = $uid;
        $data_array['created_date'] = DateUtil::getDateNow();
        $data_array['updated_user'] = $uid;
        $data_array['updated_date'] = DateUtil::getDateNow();
        return $data_array;
    }

    public function initBaseUpdateData($data_array)
    {
        $data_array['updated_user'] = ControllerUtils::getUserIdSession();
        $data_array['updated_date'] = DateUtil::getDateNow();
        return $data_array;
    }

    public function initBaseCreateDataRestApi($data, $uid)
    {
        $data->setCreatedUser($uid);
        $data->setCreatedDate(DateUtil::getDateNow());
        $data->setUpdatedUser($uid);
        $data->setUpdatedDate(DateUtil::getDateNow());
        return $data;
    }

    public function initBaseUpdateDataRestApi($data, $uid)
    {
        $data->setUpdatedUser($uid);
        $data->setUpdatedDate(DateUtil::getDateNow());
        return $data;
    }

    public function initSearchParam($object)
    {
        $tableFiedArray = $object->getTableField();
        $tableOptionalFiedArray = $object->getTableOptionalField();

        $q_parameter = array();
        foreach ($tableFiedArray as $key => $dataType) {
            $paramAtt = 'q_' . $key;
            if (FilterUtil::filterGetString($paramAtt)) {
                $q_parameter[$paramAtt] = FilterUtil::filterGetString($paramAtt);
            }
        }
        foreach ($tableOptionalFiedArray as $keyOp => $dataTypeOp) {
            $paramAttOp = 'q_' . $keyOp;
            if (FilterUtil::filterGetString($paramAttOp)) {
                $q_parameter[$paramAttOp] = FilterUtil::filterGetString($paramAttOp);
            }
        }


        //init sort order
        if (FilterUtil::filterGetString('sortField')) {
            $q_parameter['sortField'] = FilterUtil::filterGetString('sortField');
        }
        if (FilterUtil::filterGetString('sortMode')) {
            $q_parameter['sortMode'] = FilterUtil::filterGetString('sortMode');
        }


        return $q_parameter;
    }

    private function checkView($pathName)
    {
        if (file_exists(__SITE_PATH . "/application/views/" . $this->theme . "/" . $pathName . ".php")) {
            return true;
        } else {
            return false;
        }
    }

    //json header
    public function requiredJsonHeaders()
    {
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
    }

    public function requiredJsonHeadersGet()
    {
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: access");
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json; charset=UTF-8');
    }

    public function requiredJsonHeadersPost()
    {
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    public function getJsonData($assoc = false)
    {
        if ($assoc) {
            return json_decode(file_get_contents('php://input'), true);
        } else {
            return json_decode(file_get_contents("php://input"));
        }
    }

    public function getRequestHeaders()
    {
        return apache_request_headers();
    }

    public function getDefaultResponse($isOk=true)
    {
        if ($isOk) {
            return array(SystemConstant::SERVER_STATUS_ATT => true, SystemConstant::SERVER_STATUS_CODE_ATT => 200, SystemConstant::SERVER_MSG_ATT => "");
        }
        return array(SystemConstant::SERVER_STATUS_ATT => false, SystemConstant::SERVER_STATUS_CODE_ATT => 200, SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('error.error_something_wrong'));
    }

    public function setResponseStatus($data = array(), $status = true, $msg = "")
    {
        $data[SystemConstant::SERVER_STATUS_ATT] = $status;
        $data[SystemConstant::SERVER_MSG_ATT] = $msg;
        $data[SystemConstant::SERVER_STATUS_CODE_ATT] = 200;

        return $data;
    }

    public function jsonResponse($data = array())
    {
        echo json_encode($data);
    }

    public function jsonResponseApi($data = array())
    {
        if ($data[SystemConstant::SERVER_STATUS_CODE_ATT] == SystemConstant::HTTP_OK) {
            echo json_encode($data[SystemConstant::SERVER_RESPONSE_DATA]);
        } else {
            echo json_encode($data);
        }
    }

}