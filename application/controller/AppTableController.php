<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 30/1/2016
 * Time: 9:41 PM
 */

namespace application\controller;

use application\core\AppController as BaseController;
use application\core\BaseModel;
use application\core\BaseValidator;
use application\model\AppTable;
use application\service\AppTableService;
use application\service\PermissionService;
use application\util\AppUtil;
use application\util\AppUtil as AppUtils;
use application\util\ControllerUtil as ControllerUtils;
use application\util\DateUtils;
use application\util\FilterUtils as FilterUtil;
use application\util\MessageUtils;
use application\util\SystemConstant;
use application\validator\AppTableValidator;


class AppTableController extends BaseController
{
    private $appTableService;
    private $APP_TABLE_LIST_VIEW = 'app_table/appTableList';
    private $APP_TABLE_ADD_VIEW = 'app_table/appTable';
    private $appTableColunm = null;
    private $appTableColunmMetaData = array();
    private $appTableColunmCount = 0;

    private $appTableName;
    private $appTableBaseField;
    private $appTableModuleName;
    private $appTableModuleSubName;
    private $modelPath;
    private $servicePath;
    private $serviceInterfacePath;
    private $controllerlPath;
    private $validatorPath;
    private $listPath;
    private $viewPath;
    private $msgPath;
    private $routePath;
    private $postTheme;

    private $appPermissionService;

    private $isRequireAuditInfo = true;

    private $autoGenerateTextWarn = "/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/";
    /**
     * @var PermissionService
     */
    private $permssionService;

    public function __construct($databaseConnection)
    {

        $this->setDbConn($databaseConnection);
        $this->appTableService = new AppTableService($this->getDbConn());
        $this->permssionService = new PermissionService($this->getDbConn());
        $this->isAuthRequired = false;
        $this->headerContentType = SystemConstant::CONTENT_TYPE_TEXT_HTML;
    }

    public function __destruct()
    {
        unset($this->appTableService);
    }

    public function crudAdd()
    {
        $this->pushDataToView['appTable'] = new AppTable();
        $this->loadView($this->APP_TABLE_ADD_VIEW, $this->pushDataToView);
    }

    public function crudAddProcess()
    {
        $appTable = new AppTable();
        $appTable->populatePostData();

        $validator = new AppTableValidator($appTable);
        if ($validator->getValidationErrors()) {
            $this->loadView($this->APP_TABLE_ADD_VIEW, $validator->getValidationErrors());
        } else {

            $isCreateModel = FilterUtil::filterPostInt('model');
            $isCreateService = FilterUtil::filterPostInt('service');
            $isValidator = FilterUtil::filterPostInt('validator');
            $isCreateControler = FilterUtil::filterPostInt('controller');
            $isCreateList = FilterUtil::filterPostInt('vlist');
            $isCreateView = FilterUtil::filterPostInt('vview');
            $isCreateMsg = FilterUtil::filterPostInt('vmsg');
            $isCreateRoute = FilterUtil::filterPostInt('vroute');

            $this->postTheme = FilterUtil::filterPostString('vtheme') ? FilterUtil::filterPostString('vtheme') : 'default';
            $this->isRequireAuditInfo = FilterUtil::filterPostInt('auditInfo') ? true : false;

            $this->appTableName = $appTable->app_table_name;
            $this->appTableBaseField = $appTable->getTableBaseField();
            $this->appTableColunm = $this->appTableService->getTableColunm($this->appTableName);
            $this->appTableColunmMetaData = $this->appTableService->getTableColunmMetaData($this->appTableName);

            /*
             * Array
                (
                    [Field] => id
                    [Type] => int(11)
                    [Null] => NO
                    [Key] => PRI
                    [Default] =>
                    [Extra] => auto_increment
                    [vType] => int
                    [vLength] => 11
                )
             */


            $this->appTableColunmCount = count($this->appTableColunmMetaData);
            $this->appTableModuleName = AppUtils::genPublicMethodName($this->appTableName);
            $this->appTableModuleSubName = AppUtils::genModuleNameFormat($this->appTableName);
            $msgJsonGen = null;
            $routListGen = null;


            if ($isCreateModel) {
                $this->modelPath = __SITE_PATH . '/application/model/' . $this->appTableModuleName . '.php';
                $this->createModelFile($appTable);
            }
            if ($isCreateService) {
                $this->serviceInterfacePath = __SITE_PATH . '/application/serviceInterface/' . $this->appTableModuleName . 'ServiceInterface.php';
                $this->createServiceInterfaceFile();

                $this->servicePath = __SITE_PATH . '/application/service/' . $this->appTableModuleName . 'Service.php';
                $this->createServiceFile($appTable);
            }
            if ($isValidator) {
                $this->validatorPath = __SITE_PATH . '/application/validator/' . $this->appTableModuleName . 'Validator.php';
                $this->createValidatorFile();
            }
            if ($isCreateControler) {
                $this->controllerlPath = __SITE_PATH . '/application/controller/' . $this->appTableModuleName . 'Controller.php';
                $this->createControllerFile($appTable);
            }

            //create frontend vue
            $frontendPath = __SITE_PATH . '/application/views/' . $this->postTheme . '/vue-template/' . $this->appTableName;
            if ($isCreateList) {
//                $this->listPath = __SITE_PATH . '/application/views/' . $this->postTheme . '/vue-template/' . $this->appTableModuleName . '.vue';
//                $this->listPath = __SITE_PATH . '/application/views/' . $this->postTheme . '/vue-template/' . $this->appTableName . '/' . $this->appTableModuleName . '.vue';

                $this->listPath = $frontendPath . '/' . $this->appTableModuleName . '.vue';

                //create folder if it doesn't already exist
                if (!file_exists($frontendPath)) {
                    mkdir($frontendPath, 0777, true);
                }
                $this->createListFile($appTable);
            }
            if ($isCreateView) {
                $this->viewPath = $frontendPath . '/' . $this->appTableModuleName . 'Form.vue';
                if (!file_exists($frontendPath)) {
                    mkdir($frontendPath, 0777, true);
                }
                $this->createViewFile($appTable);
            }
            //create rote, menu, service
            if ($isCreateList || $isCreateView) {

                $this->createFrontendFile($appTable, $frontendPath);
            }
            //End

            if ($isCreateMsg) {
//                $this->msgPath =  __SITE_PATH.'/resources/lang/th/model.php';
                $msgJsonGen = $this->createMsgFile($appTable);
            }
            if ($isCreateRoute) {
                $this->routePath = __SITE_PATH . '/application/route/api.php';
//                $this->createRouteFile($appTable);
                $routListGen = $this->createRouteFile($appTable);
            }

            //create crud permission
            $this->permssionService->createCrudPermission($this->appTableName);


            //test
            $this->pushDataToView['msgJsonGen'] = $msgJsonGen;
            $this->pushDataToView['routListGen'] = $routListGen;
            $this->pushDataToView['appTable'] = $appTable;


            $this->loadView($this->APP_TABLE_ADD_VIEW, $this->pushDataToView);
        }
    }

    public function addApi()
    {
        $productionMode = MessageUtils::getConfig('production_mode');
        if (!$productionMode) {
            $jsonData = $this->getJsonData(false);
            $appTable = new AppTable($jsonData);
            $validator = new AppTableValidator($appTable);
            if ($validator->getValidationErrors()) {
                jsonResponse($validator->getValidationErrors());
            } else {
                $isCreateModel = isset($jsonData->model) ? $jsonData->model : false;
                $isCreateService = isset($jsonData->service) ? $jsonData->service : false;
                $isValidator = isset($jsonData->validator) ? $jsonData->validator : false;
                $isCreateControler = isset($jsonData->controller) ? $jsonData->controller : false;
                $isCreateMsg = isset($jsonData->message) ? $jsonData->message : false;
                $isCreateRoute = isset($jsonData->route) ? $jsonData->route : false;
                $isCreateCrudPermission = isset($jsonData->crudPermission) ? $jsonData->crudPermission : false;
                $this->isRequireAuditInfo = isset($jsonData->auditInfo) ? $jsonData->auditInfo : false;;

                $this->appTableName = $appTable->app_table_name;
                $this->appTableBaseField = $appTable->getTableBaseField();
                $this->appTableColunm = $this->appTableService->getTableColunm($this->appTableName);
                $this->appTableColunmMetaData = $this->appTableService->getTableColunmMetaData($this->appTableName);

                if (count($this->appTableColunm) > 0) {
                    $this->appTableColunmCount = count($this->appTableColunmMetaData);
                    $this->appTableModuleName = AppUtils::genPublicMethodName($this->appTableName);
                    $this->appTableModuleSubName = AppUtils::genModuleNameFormat($this->appTableName);
                    $msgJsonGen = null;
                    $routListGen = null;


                   if ($isCreateModel) {
                       $this->modelPath = __SITE_PATH . '/application/model/' . $this->appTableModuleName . '.php';
                       $this->createModelFile($appTable);
                   }
                   if ($isCreateService) {
                       $this->serviceInterfacePath = __SITE_PATH . '/application/serviceInterface/' . $this->appTableModuleName . 'ServiceInterface.php';
                       $this->createServiceInterfaceFile();

                       $this->servicePath = __SITE_PATH . '/application/service/' . $this->appTableModuleName . 'Service.php';
                       $this->createServiceFile($appTable);
                   }
                   if ($isValidator) {
                       $this->validatorPath = __SITE_PATH . '/application/validator/' . $this->appTableModuleName . 'Validator.php';
                       $this->createValidatorFile();
                   }
                   if ($isCreateControler) {
                       $this->controllerlPath = __SITE_PATH . '/application/controller/' . $this->appTableModuleName . 'Controller.php';
                       $this->createControllerFile($appTable, $isCreateCrudPermission);
                   }


                    if ($isCreateMsg) {
                        $msgJsonGen = $this->createMsgFileApi($appTable);
                    }
                    if ($isCreateRoute) {
                        $this->routePath = __SITE_PATH . '/application/route/api.php';
                        $routListGen = $this->createRouteFileApi($appTable, $isCreateCrudPermission);
                    }
                    //create crud permission
                    if ($isCreateCrudPermission) {
                        $this->permssionService->createCrudPermission($this->appTableName);
                    }
                    //test
                    $this->pushDataToView['msgJsonGen'] = $msgJsonGen;
                    $this->pushDataToView['routListGen'] = $routListGen;
                    jsonResponse($this->pushDataToView);
                }
            }
        }
        jsonResponse($this->getDefaultResponse(false));
    }

    private function isThisFileCanOverwrite($filename)
    {
        $isCanWrite = false;
        if (file_exists($filename)) {
            $file = AppUtils::parseFile($filename);
            if ($file != null) {
                $i = 0;
                while ($file->valid()) {
                    $i++;
                    $line = $file->fgets();
                    if ($i == 2) {
                        if (trim($line) === trim($this->autoGenerateTextWarn)) {
                            $isCanWrite = true;
                        }
                        break;
                    }
                }
                //don't forget to free the file handle.
                $file = null;
            }
        } else {
            $isCanWrite = true;
        }


        return $isCanWrite;
    }

    private function createModelFile(AppTable $appTable)
    {

        if ($this->isThisFileCanOverwrite($this->modelPath)) {

            $objFopen = fopen($this->modelPath, 'w');

            $t = "<?php" . "\r\n";
            $t .= $this->autoGenerateTextWarn . "\r\n";
            $t .= "" . "\r\n";
            $t .= "namespace application\\model;" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "use application\\core\\BaseModel;" . "\r\n";
            $t .= "class " . $this->appTableModuleName . " extends BaseModel" . "\r\n";
            $t .= "{" . "\r\n";

            /* attribute */
            $t .= "    public static $" . "tableName = '" . $appTable->app_table_name . "';" . "\r\n";

            /* __construct */
            $t .= "    public function __construct(\stdClass $" . "jsonData = null, $" . "uid = null, $" . "isUpdate = false)" . "\r\n";
            $t .= "    {" . " \r\n";

            if (!$this->isRequireAuditInfo) {
                $t .= "       //not use audit info" . " \r\n";
                $t .= "        $" . "this->setAuditInfo(false);" . " \r\n";
                $t .= "" . " \r\n";
            }

            /* init data type for field*/
            $t .= "        /* init data type for field*/" . "\r\n";
            $t .= "        $" . "this->setTableField(array(" . "\r\n";
            foreach ($this->appTableColunmMetaData as $colunmMeta) {

                if ($colunmMeta['Field'] != 'status') {
                    $t .= "            '" . $colunmMeta['Field'] . "' => " . BaseModel::getColunmTypeStringByMysqlType($colunmMeta['vType'], $colunmMeta['Extra']) . "," . "\r\n";
                } else {
                    $t .= "            'status' => self::TYPE_BOOLEAN," . "\r\n";
                }
            }
            $t .= "        ));" . " \r\n";

            $t .= "" . " \r\n";

            /* init data type for field use in update mode*/
            $t .= "        /* init data type for field use in update mode*/" . "\r\n";
            $t .= "        $" . "this->setTableFieldForEdit(array(" . "\r\n";

            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                if (!in_array($colunmMeta['Field'], $this->appTableBaseField)) {
                    if ($colunmMeta['Field'] != 'status') {
                        $t .= "            '" . $colunmMeta['Field'] . "' => " . BaseModel::getColunmTypeStringByMysqlType($colunmMeta['vType'], $colunmMeta['Extra']) . "," . "\r\n";
                    } else {
                        $t .= "            'status' => self::TYPE_BOOLEAN," . "\r\n";
                    }

                }
            }
            if ($this->isRequireAuditInfo) {
                //audit updated
                $t .= "            'updated_user' => self::TYPE_INTEGER," . "\r\n";
                $t .= "            'updated_at' => self::TYPE_DATE_TIME" . "\r\n";
            }

            $t .= "        ));" . "\r\n";
            $t .= "" . "\r\n";

            /* init optional field*/
            $t .= "        /* init optional field*/" . "\r\n";
            $t .= "        $" . "this->setTableOptionalField(array(" . "\r\n";
            $t .= "            //'field_name_option'," . "\r\n";
            $t .= "        ));" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "        $" . "this->populate($" . "jsonData, $" . "this, $" . "uid, $" . "isUpdate);" . "\r\n";
            $t .= "    }" . "\r\n";
            $t .= "" . "\r\n";

            /* getTableName */
            $t .= "    public static function getTableName()" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        return self::$" . "tableName;" . "\r\n";
            $t .= "    }" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "}";//end of file
            fwrite($objFopen, $t);
            fclose($objFopen);
        }

    }

    private function createServiceInterfaceFile()
    {
        if ($this->isThisFileCanOverwrite($this->serviceInterfacePath)) {
            $objFopen = fopen($this->serviceInterfacePath, 'w');
            $t = "<?php" . "\r\n";
            $t .= $this->autoGenerateTextWarn . "\r\n";
            $t .= "namespace application\\serviceInterface;" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "use application\\core\\AppBaseInterface;" . "\r\n";
            $t .= "interface " . $this->appTableModuleName . "ServiceInterface extends AppBaseInterface" . "\r\n";
            $t .= "{" . "\r\n";
            $t .= "    //public function manualMethodList($" . "param);" . "\r\n";
            $t .= "}";//end of file

            fwrite($objFopen, $t);
            fclose($objFopen);
        }
    }

    private function createServiceFile(AppTable $appTable)
    {
        if ($this->isThisFileCanOverwrite($this->servicePath)) {
            $objFopen = fopen($this->servicePath, 'w');
            $t = "<?php" . "\r\n";
            $t .= $this->autoGenerateTextWarn . "\r\n";
            $t .= "namespace application\\service;" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "use application\\core\\BaseDatabaseSupport;" . "\r\n";
            $t .= "use application\\serviceInterface\\" . $this->appTableModuleName . "ServiceInterface;" . "\r\n";
            $t .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $t .= "class " . $this->appTableModuleName . "Service extends BaseDatabaseSupport implements " . $this->appTableModuleName . "ServiceInterface" . "\r\n";
            $t .= "{" . "\r\n";

            $t .= "    protected $" . "tableName = '" . $appTable->app_table_name . "';" . "\r\n";
            $t .= "" . "\r\n";

            /* __construct */
            $t .= "    public function __construct($" . "dbConn){" . "\r\n";
            $t .= "        $" . "this->setDbh($" . "dbConn);" . "\r\n";
            $t .= "    }" . "\r\n";

            /* findAll */
            $t .= "    public function findAll($" . "perpage=0, $" . "q_parameter=array())" . "\r\n";
            $t .= "    {" . "\r\n";
//        $t .= "        $"."query = \"SELECT * FROM \".$"."this->tableName;"."\r\n";

            $t .= "        //if have param" . "\r\n";
            $t .= "        $" . "data_bind_where = null;" . "\r\n";
            $t .= "" . "\r\n";

            $isFirst = false;
//            foreach ($this->appTableColunm as $field) {
//                if (!$isFirst) {
//                    $t .= "        $" . "query = \"SELECT " . $appTable->app_table_name . ".`" . $field . "` AS `" . $field . "` \";" . "\r\n";
//                    $isFirst = true;
//                } else {
//                    $t .= "        $" . "query .=\"," . $appTable->app_table_name . ".`" . $field . "` AS `" . $field . "` \";" . "\r\n";
//                }
//            }
            $t .= "        $" . "query = \"SELECT *  \";" . "\r\n";

            $t .= "" . "\r\n";
            $t .= "        $" . "query .=\"FROM " . $appTable->app_table_name . " AS " . $appTable->app_table_name . " \";" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "		//default where query" . "\r\n";
            $t .= "        $" . "query .=\" WHERE " . $appTable->app_table_name . ".`id` IS NOT NULL \";" . "\r\n";
            $t .= "		//custom where query" . "\r\n";
            $t .= "       //$" . "query .= \"WHERE " . $appTable->app_table_name . ".custom_field =:customParam \";" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "        //gen additional query and sort order" . "\r\n";
//            $t .= "       $" . "additionalParam = $" . "this->genAdditionalParamAndWhereForListPage($" . "q_parameter, $" . "this->tableName);" . "\r\n";
            $t .= "       $" . "additionalParam = $" . "this->genAdditionalParamAndWhereForListPageV2($" . "q_parameter, new " . $this->appTableModuleName . "());" . "\r\n";
            $t .= "       if(!empty($" . "additionalParam)){" . "\r\n";
            $t .= "           if(!empty($" . "additionalParam['additional_query'])){" . "\r\n";
            $t .= "               $" . "query .= $" . "additionalParam['additional_query'];" . "\r\n";
            $t .= "           }" . "\r\n";
            $t .= "           if(!empty($" . "additionalParam['where_bind'])){" . "\r\n";
            $t .= "               $" . "data_bind_where = $" . "additionalParam['where_bind'];" . "\r\n";
            $t .= "           }" . "\r\n";
            $t .= "       }" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "        //custom where paramiter" . "\r\n";
            $t .= "       // $" . "data_bind_where['custom_field']=$" . "paramValue;" . "\r\n";
            $t .= "       //end" . "\r\n";

            $t .= "        //paging buider" . "\r\n";
            $t .= "        if($" . "perpage>0){" . "\r\n";
            $t .= "            $" . "query .= $" . "this->pagingHelper($" . "query, $" . "perpage, $" . "data_bind_where);" . "\r\n";
            $t .= "        }" . "\r\n";

            $t .= "        //regular query" . "\r\n";
            $t .= "        $" . "this->query($" . "query);" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "        //START BIND VALUE FOR REGULAR QUERY" . "\r\n";
            $t .= "        //$" . "this->bind(\":q_name\", \"%\".$" . "q_parameter['q_name'].\"%\");//bind param for 'LIKE'" . "\r\n";
            $t .= "	     //$" . "this->bind(\":q_name\", $" . "q_parameter['q_name']);//bind param for '='" . "\r\n";

            /*
            foreach($this->appTableColunm as $fieldBind) {
                $paramBind = 'q_' . $fieldBind;

                $t .= "        if(isset($"."q_parameter['".$paramBind."']) && $"."q_parameter['".$paramBind."']!=''){" . "\r\n";
                $t .= "            $"."this->bind(\":".$paramBind."\", \"%\".$"."q_parameter['".$paramBind."'].\"%\");" . "\r\n";
                $t .= "        }" . "\r\n";
            }
            */
            $t .= "        //END BIND VALUE FOR REGULAR QUERY" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "        //bind param for search param" . "\r\n";
            $t .= "        $" . "this->genBindParamAndWhereForListPage($" . "data_bind_where);" . "\r\n";
            $t .= "" . "\r\n";

//            $t .= "        //Return as Object Class" . "\r\n";
//            $t .= "        /*" . "\r\n";
//            $t .= "        $" . "resaultList =  $" . "this->resultset();" . "\r\n";
//            $t .= "		if (is_array($" . "resaultList) || is_object($" . "resaultList)){" . "\r\n";
//            $t .= "            $" . "findList = array();" . "\r\n";
//            $t .= "            foreach($" . "resaultList as $" . "obj){" . "\r\n";
//            $t .= "                $" . "singleObj = null;" . "\r\n";
//            $t .= "                $" . "singleObj = new " . $this->appTableModuleName . "($" . "obj);" . "\r\n";
//            $t .= "                array_push($" . "findList, $" . "singleObj);" . "\r\n";
//            $t .= "            }" . "\r\n";
//            $t .= "            return $" . "findList;" . "\r\n";
//            $t .= "        }" . "\r\n";
//            $t .= "        */" . "\r\n";

            //cast tinyint(1) to boolean
            $boolTypeCast = "";
            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                if ($colunmMeta['Type'] == 'tinyint(1)') {//assume as boolean type
                    $boolTypeCast .= "            $" . "t->" . $colunmMeta['Field'] . " = boolval($" . "t->" . $colunmMeta['Field'] . ");" . "\r\n";
                }
            }
            if ($boolTypeCast) {
                $t .= "        $" . "list = [];" . "\r\n";
                $t .= "        $" . "reasult = $" . "this->list();" . "\r\n";
                $t .= "        foreach ($" . "reasult AS $" . "t) {" . "\r\n";
                $t .= $boolTypeCast;
                $t .= "            array_push($" . "list, $" . "t);" . "\r\n";
                $t .= "        }" . "\r\n";
                $t .= "        return $" . "list;" . "\r\n";
            } else {
                $t .= "        return  $" . "this->list();" . "\r\n";
            }


            $t .= "    }" . "\r\n";
            $t .= "" . "\r\n";

            /* findById */
            $t .= "    public function findById($" . "id)" . "\r\n";
            $t .= "    {" . "\r\n";
//        $t .= "        $"."query = \"SELECT * FROM \".$"."this->tableName.\" WHERE id=:id\";"."\r\n";
            $isFirst = false;
//            foreach ($this->appTableColunm as $findByField) {
//                if (!$isFirst) {
//                    $t .= "        $" . "query = \"SELECT " . $appTable->app_table_name . ".`" . $findByField . "` AS `" . $findByField . "` \";" . "\r\n";
//                    $isFirst = true;
//                } else {
//                    $t .= "        $" . "query .=\"," . $appTable->app_table_name . ".`" . $findByField . "` AS `" . $findByField . "` \";" . "\r\n";
//                }
//            }
            $t .= "        $" . "query = \"SELECT *  \";" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "        $" . "query .=\"FROM " . $appTable->app_table_name . " AS " . $appTable->app_table_name . " \";" . "\r\n";
            $t .= "        $" . "query .=\"WHERE " . $appTable->app_table_name . ".`id`=:id \";" . "\r\n";
            $t .= "" . "\r\n";


            $t .= "        $" . "this->query($" . "query);" . "\r\n";
            $t .= "        $" . "this->bind(\":id\", (int)$" . "id);" . "\r\n";


//            $t .= "        //Return as Object Class" . "\r\n";
//            $t .= "        /*" . "\r\n";
//            $t .= "        $" . "result =  $" . "this->single();" . "\r\n";
//            $t .= "		if (is_array($" . "result) || is_object($" . "result)){" . "\r\n";
//            $t .= "            $" . $this->appTableModuleSubName . " = new " . $this->appTableModuleName . "($" . "result);" . "\r\n";
//            $t .= "            return $" . "$this->appTableModuleSubName;" . "\r\n";
//            $t .= "        }" . "\r\n";
//            $t .= "        */" . "\r\n";

            if ($boolTypeCast) {
                $t .= "        $" . "t = $" . "this->single();" . "\r\n";
                $t .= "        if ($" . "t) {" . "\r\n";
                $t .= $boolTypeCast;
                $t .= "        }" . "\r\n";
                $t .= "        return $" . "t;" . "\r\n";
            } else {
                $t .= "        return  $" . "this->single();" . "\r\n";
            }

            $t .= "    }" . "\r\n";

            /* deleteById */
            $t .= "    public function deleteById($" . "id)" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "query = \"DELETE FROM \".$" . "this->tableName.\" WHERE id=:id\";" . "\r\n";
            $t .= "        $" . "this->query($" . "query);" . "\r\n";
            $t .= "        $" . "this->bind(\":id\", (int)$" . "id);" . "\r\n";
            $t .= "        return $" . "this->execute();" . "\r\n";
            $t .= "    }" . "\r\n";

            /* createByArray */
            $t .= "    public function createByArray($" . "data_array)" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        return $" . "this->insertHelper($" . "this->tableName, $" . "data_array);" . "\r\n";
            $t .= "    }" . "\r\n";

            /* createByObject */
            $t .= "    public function createByObject($" . "oject)" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        return $" . "this->insertObjectHelper($" . "oject);" . "\r\n";
            $t .= "    }" . "\r\n";

            /* update */
            $t .= "    public function update($" . "data_array, $" . "where_array, $" . "whereType = 'AND')" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        return $" . "this->updateHelper($" . "this->tableName, $" . "data_array, $" . "where_array, $" . "whereType);" . "\r\n";
            $t .= "    }" . "\r\n";

            /* updateByObject */
            $t .= "    public function updateByObject($" . "object, $" . "where_array, $" . "whereType = 'AND')" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        return $" . "this->updateObjectHelper($" . "object, $" . "where_array, $" . "whereType);" . "\r\n";
            $t .= "    }" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "}";//end of file

            fwrite($objFopen, $t);
            fclose($objFopen);
        }
    }

    private function createValidatorFile()
    {
        if ($this->isThisFileCanOverwrite($this->validatorPath)) {
            $objFopen = fopen($this->validatorPath, 'w');
            $t = "<?php" . "\r\n";
            $t .= $this->autoGenerateTextWarn . "\r\n";
            $t .= "namespace application\\validator;" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "use application\\core\\BaseValidator;" . "\r\n";
            $t .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $t .= "class " . $this->appTableModuleName . "Validator extends BaseValidator" . "\r\n";
            $t .= "{" . "\r\n";
            $t .= "    public function __construct($this->appTableModuleName $" . $this->appTableModuleSubName . ")" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        //call parent construct" . "\r\n";
            $t .= "        parent::__construct();" . "\r\n";
            $t .= "        $" . "this->objToValidate = $" . $this->appTableModuleSubName . ";" . "\r\n";

            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                if (!in_array($colunmMeta['Field'], $this->appTableBaseField)) {

                    if ($colunmMeta['Null'] == 'NO') {
                        $t .= "        $" . "this->validateField('" . $colunmMeta['Field'] . "', self::VALIDATE_REQUIRED);" . "\r\n";
                    }
                    $typeValidate = BaseValidator::getColunmValidatorByMysqlType($colunmMeta['vType']);
                    if (!AppUtils::isEmpty($typeValidate)) {

                        if ($colunmMeta['Type'] == 'tinyint(1)') {//assume as boolean type
                            $typeValidate = "self::VALIDATE_BOOLEAN";
                        }

                        $t .= "        $" . "this->validateField('" . $colunmMeta['Field'] . "', " . $typeValidate . ");" . "\r\n";
                    }
                }
            }
            $t .= "" . "\r\n";
            $t .= "        //Custom Validate" . "\r\n";
            $t .= "        /*" . "\r\n";
            $t .= "        if($" . $this->appTableModuleSubName . "->getPrice < $" . $this->appTableModuleSubName . "->getDiscount){" . "\r\n";
            $t .= "          $" . "this->addError('price', 'Price Can't Must than Discount');" . "\r\n";
            $t .= "        }" . "\r\n";
            $t .= "        */" . "\r\n";

            $t .= "    }" . "\r\n";

            $t .= "}";//end of file

            fwrite($objFopen, $t);
            fclose($objFopen);
        }
    }

    private function createControllerFile(AppTable $appTable, $isHaveValidator = true)
    {
        if ($this->isThisFileCanOverwrite($this->controllerlPath)) {
            $objFopen = fopen($this->controllerlPath, 'w');
            $t = "<?php" . "\r\n";
            $t .= $this->autoGenerateTextWarn . "\r\n";
            $t .= "/**" . "\r\n";
            $t .= " * Created by Bekaku Php Back End System." . "\r\n";
            $t .= " * Date: " . DateUtils::getDateNow(true) . "\r\n";
            $t .= " */" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "namespace application\\controller;" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "use application\\core\\AppController;" . "\r\n";
            $t .= "use application\\util\\FilterUtils;" . "\r\n";
            $t .= "use application\\util\\i18next;" . "\r\n";
            $t .= "use application\\util\\SystemConstant;" . "\r\n";
            $t .= "use application\\util\\SecurityUtil;" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $t .= "use application\\service\\" . $this->appTableModuleName . "Service " . ";" . "\r\n";
            if ($isHaveValidator) {
                $t .= "use application\\validator\\" . $this->appTableModuleName . "Validator " . ";" . "\r\n";
            }

            $t .= "class " . $this->appTableModuleName . "Controller extends  AppController" . "\r\n";
            $t .= "{" . "\r\n";
            $t .= "    /**" . "\r\n";
            $t .= "    * @var " . $this->appTableModuleName . "Service" . "\r\n";
            $t .= "    */" . "\r\n";
            $t .= "    private $" . $this->appTableModuleSubName . "Service;" . "\r\n";

            /*__construct*/
            $t .= "    public function __construct($" . "databaseConnection)" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "this->setDbConn($" . "databaseConnection);" . "\r\n";
            $t .= "        $" . "this->" . $this->appTableModuleSubName . "Service = new " . $this->appTableModuleName . "Service($" . "this->getDbConn());" . "\r\n";
            $t .= "" . "\r\n";

            $t .= "    }" . "\r\n";

            /*__destruct*/
            $t .= "    public function __destruct()" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "this->setDbConn(null);" . "\r\n";
            $t .= "        unset($" . "this->" . $this->appTableModuleSubName . "Service);" . "\r\n";
            $t .= "    }" . "\r\n";

            /*crudList*/
            $t .= "    public function crudList()" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;" . "\r\n";
            $t .= "        $" . "this->setRowPerPage($" . "perPage);" . "\r\n";
            $t .= "        $" . "q_parameter = $" . "this->initSearchParam(new " . $this->appTableModuleName . "());" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse();" . "\r\n";
            $t .= "        $" . "this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $" . "this->" . $this->appTableModuleSubName . "Service->findAll($" . "this->getRowPerPage(), $" . "q_parameter);" . "\r\n";
            $t .= "        $" . "this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $" . "this->" . $this->appTableModuleSubName . "Service->getTotalPaging();" . "\r\n";
            $t .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";

            $t .= "    }" . "\r\n";
            //end crudList

            /*crudAdd*/
            $t .= "    public function crudAdd()" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "uid = SecurityUtil::getAppuserIdFromJwtPayload();" . "\r\n";
            $t .= "        $" . "jsonData = $" . "this->getJsonData(false);" . "\r\n";
            $t .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(false);" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "        if(!empty($" . "jsonData) && !empty($" . "uid)) {" . "\r\n";
            $t .= "           $" . "entity = new " . $this->appTableModuleName . "($" . "jsonData, $" . "uid, false);" . "\r\n";
            if ($isHaveValidator) {
                $t .= "           $" . "validator = new " . $this->appTableModuleName . "Validator($" . "entity);" . "\r\n";

                $t .= "           if ($" . "validator->getValidationErrors()) {" . "\r\n";
                $t .= "               jsonResponse($" . "this->setResponseStatus($" . "validator->getValidationErrors(), false, null), 400);" . "\r\n";
                $t .= "           } else {" . "\r\n";
            }
            $t .= "               $" . "lastInsertId = $" . "this->" . $this->appTableModuleSubName . "Service->createByObject($" . "entity);" . "\r\n";
            $t .= "               if ($" . "lastInsertId) {" . "\r\n";
//            $t .= "                   $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, true, i18next::getTranslation(('success.insert_succesfull')));" . "\r\n";
            $t .= "                    $" . "this->pushDataToView = $" . "this->setResponseStatus([SystemConstant::ENTITY_ATT => $" . "this->" . $this->appTableModuleSubName . "Service->findById($" . "lastInsertId)], true, i18next::getTranslation(('success.insert_succesfull')));" . "\r\n";
            $t .= "                }" . "\r\n";
            if ($isHaveValidator) {
                $t .= "           }" . "\r\n";
            }
            $t .= "        }" . "\r\n";
            $t .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $t .= "" . "\r\n";
            $t .= "    }" . "\r\n";
            //end crudAdd


            /*crudReadSingle*/
            $t .= "    public function crudReadSingle()" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);" . "\r\n";
            $t .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(false);" . "\r\n";
            $t .= "        $" . "item = null;" . "\r\n";
            $t .= "        if ($" . "id > 0) {" . "\r\n";
            $t .= "            $" . "item = $" . "this->" . $this->appTableModuleSubName . "Service->findById($" . "id);" . "\r\n";
            $t .= "            if ($" . "item) {" . "\r\n";
            $t .= "                $" . "this->pushDataToView = $" . "this->getDefaultResponse(true);" . "\r\n";
            $t .= "            }" . "\r\n";
            $t .= "        }" . "\r\n";
            $t .= "        $" . "this->pushDataToView[SystemConstant::ENTITY_ATT] = $" . "item;" . "\r\n";
            $t .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $t .= "    }" . "\r\n";
            //end crudReadSingle

            /*crudEdit*/
            $t .= "    public function crudEdit()" . "\r\n";
            $t .= "    {" . "\r\n";

            $t .= "        $" . "uid = SecurityUtil::getAppuserIdFromJwtPayload();" . "\r\n";
            $t .= "        $" . "jsonData = $" . "this->getJsonData(false);" . "\r\n";
            $t .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(false);" . "\r\n";
            $t .= "		" . "\r\n";
            $t .= "        if(!empty($" . "jsonData) && !empty($" . "uid)) {" . "\r\n";
            $t .= "           $" . $this->appTableModuleSubName . " = new " . $this->appTableModuleName . "($" . "jsonData, $" . "uid, true);" . "\r\n";
            if ($isHaveValidator) {
                $t .= "           $" . "validator = new " . $this->appTableModuleName . "Validator($" . $this->appTableModuleSubName . ");" . "\r\n";
                $t .= "           if ($" . "validator->getValidationErrors()) {" . "\r\n";
                $t .= "               jsonResponse($" . "this->setResponseStatus($" . "validator->getValidationErrors(), false, null), 400);" . "\r\n";
                $t .= "           } else {" . "\r\n";
            }
            $t .= "                if (isset($" . $this->appTableModuleSubName . "->id)) {" . "\r\n";
            $t .= "                   $" . "effectRow = $" . "this->" . $this->appTableModuleSubName . "Service->updateByObject($" . $this->appTableModuleSubName . ", array('id' => $" . $this->appTableModuleSubName . "->id));" . "\r\n";
            $t .= "                   if ($" . "effectRow) {" . "\r\n";
            $t .= "                       $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));" . "\r\n";
            $t .= "                   }" . "\r\n";
            $t .= "               }" . "\r\n";
            if ($isHaveValidator) {
                $t .= "           }" . "\r\n";
            }
            $t .= "       }" . "\r\n";

            $t .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $t .= "    }" . "\r\n";
            //end crudEditProcess


            /*crudDelete*/
            $t .= "    public function crudDelete()" . "\r\n";
            $t .= "    {" . "\r\n";
            $t .= "        $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, true, i18next::getTranslation('success.delete_succesfull'));" . "\r\n";
            $t .= "        $" . "idParams = FilterUtils::filterGetString(SystemConstant::ID_PARAMS);//paramiter format : idOfNo1_idOfNo2_idOfNo3_idOfNo4 ..." . "\r\n";
            $t .= "        $" . "idArray = explode(SystemConstant::UNDER_SCORE, $" . "idParams);" . "\r\n";
            $t .= "        if (count($" . "idArray) > 0) {" . "\r\n";
            $t .= "            foreach ($" . "idArray AS $" . "id) {" . "\r\n";
            $t .= "                $" . "entity = $" . "this->" . $this->appTableModuleSubName . "Service->findById($" . "id);" . "\r\n";
            $t .= "                if ($" . "entity) {" . "\r\n";
            $t .= "                    $" . "effectRow = $" . "this->" . $this->appTableModuleSubName . "Service->deleteById($" . "id);" . "\r\n";
            $t .= "                    if (!$" . "effectRow) {" . "\r\n";
            $t .= "                        $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));" . "\r\n";
            $t .= "                        break;" . "\r\n";
            $t .= "                    }" . "\r\n";
            $t .= "                }" . "\r\n";
            $t .= "            }" . "\r\n";
            $t .= "        }" . "\r\n";
            $t .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $t .= "    }" . "\r\n";
            //end crudDelete

            $t .= "" . "\r\n";
            $t .= "}";//end of controller file

            fwrite($objFopen, $t);
            fclose($objFopen);
        }
    }

    private function getFieldType($colunmMeta, $field)
    {
        $fieldType = BaseModel::TYPE_STRING;

        if ($colunmMeta['vType'] == 'date') {
            $fieldType = BaseModel::TYPE_DATE;
        }
        if ($colunmMeta['vType'] == 'datetime') {
            $fieldType = BaseModel::TYPE_DATE_TIME;
        } else if ($colunmMeta['vType'] == 'text') {
            $fieldType = BaseModel::TYPE_TEXT_AREA;
        } else if ($colunmMeta['vType'] == 'tinyint' || $field == 'status') {
            $fieldType = BaseModel::TYPE_BOOLEAN;
        } else if ($colunmMeta['vType'] == 'int') {
            $fieldType = BaseModel::TYPE_INTEGER;
        } else if ($field == 'img_name') {
            $fieldType = BaseModel::TYPE_IMAGE;
        }
        return $fieldType;
    }

    private function isHaveDateType()
    {
        $state = false;
        foreach ($this->appTableColunmMetaData as $colunmMeta) {
            if ($colunmMeta['vType'] == 'date') {
                $state = true;
                break;
            }
        }
        return $state;
    }

    private function createListFile(AppTable $appTable)
    {

//        if ($this->isThisFileCanOverwrite($this->listPath)) {
        $objFopen = fopen($this->listPath, 'w');
        //Start template
        $t = "<template>" . "\r\n";
        $t .= "  <v-container" . "\r\n";
        $t .= "    id=\"page-$this->appTableModuleSubName\"" . "\r\n";
        $t .= "    fluid" . "\r\n";
        $t .= "  >" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "    <base-wee-sketlon-loader" . "\r\n";
        $t .= "      :loading=\"state.loading\"" . "\r\n";
        $t .= "      type=\"table-heading, table-thead, table-tbody, table-tfoot\"" . "\r\n";
        $t .= "      :no=\"1\"" . "\r\n";
        $t .= "    />" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "    <!-- Table  -->" . "\r\n";
        $t .= "    <wee-simple-table" . "\r\n";
        $t .= "      v-if=\"!state.loading\"" . "\r\n";
        $t .= "      :headers=\"fillableHeaders\"" . "\r\n";
        $t .= "      :title=\"$" . "t('model.$appTable->app_table_name.$appTable->app_table_name')\"" . "\r\n";
        $t .= "      :tr-list=\"filteredList\"" . "\r\n";
        $t .= "      :pages=\"pages\"" . "\r\n";
        $t .= "      :sort=\"sort\"" . "\r\n";
        $t .= "      @update-search=\"searchTxt = $" . "event\"" . "\r\n";
        $t .= "      @on-item-click=\"onItemClick\"" . "\r\n";
        $t .= "      @on-item-delete=\"onBeforeDeleteItem\"" . "\r\n";
        $t .= "      @on-open-new-form=\"onOpenNewForm\"" . "\r\n";
        $t .= "      @on-advance-search=\"advanceSearch\"" . "\r\n";
        $t .= "      @on-reload-page=\"onReload\"" . "\r\n";
        $t .= "    >" . "\r\n";
        $t .= "      <!-- <template v-slot:theader></template> " . "\r\n";
        $t .= "      <" . "template v-slot" . ":tbody></" . "template> " . "\r\n";
        $t .= "      <" . "template v-slot" . ":tpaging><" . "/template>  -->" . "\r\n";
        $t .= "    </wee-simple-table>" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "    <" . AppUtil::genComponentNameFormat($appTable->app_table_name) . "-form v-model=\"entity\" :edit-mode=\"editMode\" :open=\"openNewForm\" :processing=\"isProcessing\" @close=\"openNewForm = false\" @save=\"onSave\"/>" . "\r\n";

        $t .= "    <wee-confirm ref=\"weeConfirmRef\"></wee-confirm>" . "\r\n";
        $t .= "    <wee-toast ref=\"weeToastRef\"></wee-toast>" . "\r\n";
        $t .= "  </v-container>" . "\r\n";
        $t .= "</template>" . "\r\n";
        $t .= "" . "\r\n";
        //End template

        //script
        $t .= "<script>" . "\r\n";

        //Import section
        $t .= "import { vLog } from \"@/plugins/util\";" . "\r\n";
        if ($this->isHaveDateType()) {
            $t .= "import { getDateWithDefaultFormat } from \"@/plugins/dateUtil\";" . "\r\n";
        }

        $t .= "//service" . "\r\n";
        $t .= "import $this->appTableModuleName" . "Service from \"@/api/" . $this->appTableModuleName . "Service\";" . "\r\n";
        $t .= "import useCrudApi from \"@/composition/UseCrudApi\";" . "\r\n";
        $t .= "import { toRefs, onBeforeUnmount} from \"@vue/composition-api\";" . "\r\n";

        $t .= "export default {" . "\r\n";
        $t .= "  name: \"page-$this->appTableModuleSubName\"," . "\r\n";

        //components import
        $t .= "  components: {" . "\r\n";
        $t .= "    WeeConfirm: () => import(\"@/components/WeeConfirm\")," . "\r\n";
        $t .= "    WeeToast: () => import(\"@/components/WeeToast\")," . "\r\n";
        $t .= "    WeeSimpleTable: () => import(\"@/components/WeeSimpleTable\")," . "\r\n";
        $t .= "    " . $this->appTableModuleName . "Form: () => import(\"./" . $this->appTableModuleName . "Form\")," . "\r\n";
        $t .= "  }," . "\r\n";

        //start setup()
        $t .= "  setup(props, { refs, root }) {" . "\r\n";
        $t .= "    const " . $this->appTableModuleSubName . "Service = new " . $this->appTableModuleName . "Service();" . "\r\n";
        //table header filter
        $t .= "//column, label, searchable, sortable, fillable, image, avatar status, date, datetime " . "\r\n";
        $t .= "    const tableHeaders = [" . "\r\n";
        foreach ($this->appTableColunmMetaData as $colunmMeta) {
            $field = $colunmMeta['Field'];
            if (!in_array($field, $appTable->getTableBaseField())) {
                $fieldType = $this->getFieldType($colunmMeta, $field);

                $t .= "      {" . "\r\n";
                $t .= "        column: \"$field\"," . "\r\n";
                $t .= "        label: \"model." . $appTable->app_table_name . "." . $field . "\"," . "\r\n";
                $t .= "        searchable: true," . "\r\n";
                $t .= "        sortable: true," . "\r\n";
                $t .= "        fillable: true," . "\r\n";
                if ($fieldType == BaseModel::TYPE_BOOLEAN) {
                    $t .= "    status: true," . "\r\n";
                }
                if ($fieldType == BaseModel::TYPE_DATE) {
                    $t .= "    date: true," . "\r\n";
                }
                if ($fieldType == BaseModel::TYPE_DATE_TIME) {
                    $t .= "    datetime: true," . "\r\n";
                }

                $t .= "        //linkable: {external: true}," . "\r\n";
                $t .= "      }," . "\r\n";
            }
        }
        $t .= "      {" . "\r\n";
        $t .= "        label: \"base.tool\"," . "\r\n";
        $t .= "        fillable: true," . "\r\n";
        $t .= "        baseTool: true" . "\r\n";
        $t .= "      }" . "\r\n";
        $t .= "    ];" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "    //entity" . "\r\n";
        $t .= "    const initialItem = {" . "\r\n";
        foreach ($this->appTableColunmMetaData as $colunmMeta) {
            $field = $colunmMeta['Field'];
//            if (!in_array($field, $appTable->getTableBaseField())) {
            $fieldType = $this->getFieldType($colunmMeta, $field);
            switch ($fieldType) {
                case BaseModel::TYPE_IMAGE:
                    $t .= "      $field:''," . "\r\n";
                    break;
                case BaseModel::TYPE_BOOLEAN:
                    $t .= "      $field: false," . "\r\n";
                    break;
                case BaseModel::TYPE_DATE:
                    $t .= "      $field: getDateWithDefaultFormat()," . "\r\n";
                    break;
                case BaseModel::TYPE_INTEGER:
                    $t .= "      $field: 0," . "\r\n";
                    break;
                default:
                    $t .= "      $field: ''," . "\r\n";
            }
//            }
        }
        $t .= "    };" . "\r\n";
        $t .= "" . "\r\n";
        //use crudapi composition
        $t .= "    const {" . "\r\n";
        $t .= "      crud" . "\r\n";
        $t .= "    } = useCrudApi(refs, root, " . $this->appTableModuleSubName . "Service, initialItem, tableHeaders);" . "\r\n";
        $t .= "" . "\r\n";
        //sort colunm
        $t .= "    //fell free to change sort colunm and mode" . "\r\n";
        $t .= "    //sort.column = \"id\";" . "\r\n";
        $t .= "    //sort.mode = \"ASC\";" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "    onBeforeUnmount(()=>{" . "\r\n";
        $t .= "      vLog('onBeforeUnmount')" . "\r\n";
        $t .= "    });" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "    return {" . "\r\n";
        $t .= "      ...toRefs(crud)," . "\r\n";
        $t .= "    };" . "\r\n";
        $t .= "  }" . "\r\n";
        $t .= "};" . "\r\n";
        $t .= "</script>" . "\r\n";
        //end of file

        fwrite($objFopen, $t);
        fclose($objFopen);
//        }

    }

    private function createViewFile(AppTable $appTable)
    {
//        if ($this->isThisFileCanOverwrite($this->viewPath)) {
        $objFopen = fopen($this->viewPath, 'w');
        $t = "<template>" . "\r\n";
        $t .= "    <!--      Start  Form -->" . "\r\n";
        $t .= "    <v-dialog" . "\r\n";
        $t .= "     v-if=\"entity\"" . "\r\n";
        $t .= "      v-model=\"open\"" . "\r\n";
        $t .= "      fullscreen" . "\r\n";
        $t .= "      hide-overlay" . "\r\n";
        $t .= "      transition=\"dialog-bottom-transition\"" . "\r\n";
        $t .= "      persistent" . "\r\n";
        $t .= "      scrollable" . "\r\n";
        $t .= "    >" . "\r\n";
        $t .= "      <v-card>" . "\r\n";
        $t .= "        <v-toolbar" . "\r\n";
        $t .= "          flat" . "\r\n";
        $t .= "        >" . "\r\n";
        $t .= "          <v-btn" . "\r\n";
        $t .= "            icon" . "\r\n";
        $t .= "            :loading=\"processing\"" . "\r\n";
        $t .= "            :disabled=\"processing\"" . "\r\n";
        $t .= "            @click=\"close\"" . "\r\n";
        $t .= "          >" . "\r\n";
        $t .= "            <v-icon>mdi-keyboard-backspace</v-icon>" . "\r\n";
        $t .= "          </v-btn>" . "\r\n";
        $t .= "        <v-toolbar-title>{{" . "$" . "t('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "') +' ('+(!editMode ?  $" . "t('base.addNew') : $" . "t('base.edit'))+')'}} </v-toolbar-title>" . "\r\n";
//        $t .= "          <v-toolbar-title>{{" . "$" . "t('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "')}}</v-toolbar-title>" . "\r\n";
        $t .= "          <v-spacer></v-spacer>" . "\r\n";

//        $t .= "            <v-btn" . "\r\n";
//        $t .= "              text" . "\r\n";
//        $t .= "              @click=\"close\"" . "\r\n";
//        $t .= "              :disabled=\"processing\"" . "\r\n";
//        $t .= "            > {" . "{" . "$" . "t('base.cancel') }" . "}" . "\r\n";
//        $t .= "            </v-btn>" . "\r\n";
//        $t .= "            <v-btn" . "\r\n";
//        $t .= "              text" . "\r\n";
//        $t .= "              @click=\"onSave\"" . "\r\n";
//        $t .= "              :disabled=\"processing\"" . "\r\n";
//        $t .= "            ><v-icon>mdi-lead-pencil</v-icon> {" . "{" . "$" . "t('base.save') }" . "}" . "\r\n";
//        $t .= "            </v-btn>" . "\r\n";


        $t .= "        </v-toolbar>" . "\r\n";
        $t .= "        <v-card-text>" . "\r\n";
        $t .= "              <validation-observer" . "\r\n";
        $t .= "                ref=\"form\"" . "\r\n";
        $t .= "                v-slot=\"{ handleSubmit, reset }\"" . "\r\n";
        $t .= "              >" . "\r\n";
        $t .= "                <form" . "\r\n";
        $t .= "                  @submit.prevent=\"handleSubmit(onSave)\"" . "\r\n";
        $t .= "                  @reset.prevent=\"reset\"" . "\r\n";
        $t .= "                >" . "\r\n";
        $t .= "          <v-container>" . "\r\n";
        $t .= "            <v-row>" . "\r\n";
        $t .= "" . "\r\n";
        foreach ($this->appTableColunmMetaData as $colunmMeta) {
            $field = $colunmMeta['Field'];
            if (!in_array($field, $this->appTableBaseField)) {

//                $isRequire = false;
//                $limitTextLenght = 0;
//                $typeDateClass = false;
//                $isTextArea = false;
//                $isBoolean = false;
//                $isNumber = false;

                $fieldType = BaseModel::TYPE_STRING;


                $veeValidateRules = null;
                if ($colunmMeta['Null'] == 'NO') {
//                    $isRequire = true;
                    $veeValidateRules = "required";
                }

                if (!AppUtil::isEmpty($colunmMeta['vLength']) && $colunmMeta['vLength'] > 0) {
                    $limitTextLenght = $colunmMeta['vLength'];
                    $veeValidateRules = $veeValidateRules != null ? $veeValidateRules . "|max:" . $limitTextLenght : "max:" . $limitTextLenght;
                }

                if ($colunmMeta['vType'] == 'date') {
                    $fieldType = $this->getFieldType($colunmMeta, $field);
                    $veeValidateRules = null;
                }
                if ($colunmMeta['vType'] == 'text') {
                    $fieldType = $this->getFieldType($colunmMeta, $field);
                }
                if ($colunmMeta['vType'] == 'tinyint' || $field == 'status') {
                    $fieldType = $this->getFieldType($colunmMeta, $field);
                    $veeValidateRules = null;
                }
                if ($colunmMeta['vType'] == 'int') {
                    $fieldType = $this->getFieldType($colunmMeta, $field);
                    $veeValidateRules = $veeValidateRules != null ? $veeValidateRules . "|numeric:" : "numeric";
                }
                if ($field == 'img_name') {
                    $fieldType = $this->getFieldType($colunmMeta, $field);
                }


                $t .= "              <v-col cols=\"12\">" . "\r\n";


                if ($veeValidateRules) {

                    $t .= "                  <validation-provider" . "\r\n";
                    $t .= "                    v-slot=\"{ errors }\"" . "\r\n";
                    $t .= "                    :name=\"$" . "t('model." . $appTable->app_table_name . "." . $field . "')\"" . "\r\n";
                    $t .= "                    rules=\"$veeValidateRules\"" . "\r\n";
                    $t .= "                  >" . "\r\n";
                }
                switch ($fieldType) {
                    case BaseModel::TYPE_IMAGE:
                        break;
                    case BaseModel::TYPE_BOOLEAN:
                        $t .= "                <v-switch" . "\r\n";
                        $t .= "                  v-model=\"entity.$field\"" . "\r\n";
                        $t .= "                  :label=\"entity.$field ? $" . "t('base.enable') : $" . "t('base.disable')\"" . "\r\n";
                        $t .= "                ></v-switch>" . "\r\n";
                        break;
                    case BaseModel::TYPE_DATE:
                        $t .= "                <p class='caption'>{{" . "$" . "t('model." . $appTable->app_table_name . "." . $field . "')}}</p>" . "\r\n";
                        $t .= "                <v-date-picker v-model=\"entity.$field\"></v-date-picker>" . "\r\n";
                        $t .= "" . "\r\n";
                        break;
                    case BaseModel::TYPE_TEXT_AREA:
                        $t .= "                <v-textarea" . "\r\n";
                        $t .= "                  clearable" . "\r\n";
                        $t .= "                  prepend-icon=\"mdi-pencil\"" . "\r\n";
                        $t .= "                  v-model=\"entity.$field\"" . "\r\n";
                        if ($veeValidateRules) {
                            $t .= "                  :error-messages=\"errors\"" . "\r\n";
                        }
                        $t .= "                  :placeholder=\"$" . "t('model." . $appTable->app_table_name . "." . $field . "')\"" . "\r\n";
                        $t .= "                  :label=\"$" . "t('model." . $appTable->app_table_name . "." . $field . "')\"" . "\r\n";
                        $t .= "                ></v-textarea>" . "\r\n";
                        break;
                    default:
                        //input type text
                        $t .= "                <v-text-field" . "\r\n";
                        $t .= "                  prepend-icon=\"mdi-pencil\"" . "\r\n";
                        $t .= "                  v-model=\"entity.$field\"" . "\r\n";
                        if ($veeValidateRules) {
                            $t .= "                  :error-messages=\"errors\"" . "\r\n";
                        }
                        $t .= "                  :placeholder=\"$" . "t('model." . $appTable->app_table_name . "." . $field . "')\"" . "\r\n";
                        $t .= "                  :label=\"$" . "t('model." . $appTable->app_table_name . "." . $field . "')\"" . "\r\n";
                        $t .= "                ></v-text-field>" . "\r\n";

                }

                if ($veeValidateRules) {
                    $t .= "                  </validation-provider>" . "\r\n";
                }
                $t .= "              </v-col>" . "\r\n";
                $t .= "" . "\r\n";
            }
        }
        $t .= "                <v-col cols=\"12\" class=\"mt-6\" align=\"center\">" . "\r\n";
        $t .= "                  <v-btn" . "\r\n";
        $t .= "                    text" . "\r\n";
        $t .= "                    @click=\"close\"" . "\r\n";
        $t .= "                    :disabled=\"processing\"" . "\r\n";
        $t .= "                  >" . "\r\n";
        $t .= "                    {{ $" . "t(\"base.cancel\") }}" . "\r\n";
        $t .= "                  </v-btn>" . "\r\n";
        $t .= "                  <v-btn" . "\r\n";
        $t .= "                    type=\"submit\"" . "\r\n";
        $t .= "                    text" . "\r\n";
        $t .= "                    color=\"primary\"" . "\r\n";
        $t .= "                    :disabled=\"processing\"" . "\r\n";
        $t .= "                  >" . "\r\n";
        $t .= "                    <v-icon>mdi-lead-pencil</v-icon> {{ $" . "t(\"base.save\") }}" . "\r\n";
        $t .= "                  </v-btn>" . "\r\n";
        $t .= "                </v-col>" . "\r\n";
        $t .= "" . "\r\n";

        $t .= "                        </v-row>" . "\r\n";
        $t .= "                    </v-container>" . "\r\n";
        $t .= "                </form>" . "\r\n";
        $t .= "              </validation-observer>" . "\r\n";

        $t .= "        </v-card-text>" . "\r\n";
        $t .= "      </v-card>" . "\r\n";
        $t .= "    </v-dialog>" . "\r\n";
        $t .= "</template>" . "\r\n";
        //script
        $t .= "<script>" . "\r\n";
        $t .= "import { defineComponent, reactive } from \"@vue/composition-api\";" . "\r\n";
        $t .= "export default defineComponent({" . "\r\n";
        $t .= "  props: {" . "\r\n";
        $t .= "    value: null," . "\r\n";
        $t .= "    open: {" . "\r\n";
        $t .= "      type: Boolean," . "\r\n";
        $t .= "      default: false" . "\r\n";
        $t .= "    }," . "\r\n";
        $t .= "    editMode: {" . "\r\n";
        $t .= "      type: Boolean," . "\r\n";
        $t .= "      default: false" . "\r\n";
        $t .= "    }," . "\r\n";
        $t .= "    processing: {" . "\r\n";
        $t .= "      type: Boolean," . "\r\n";
        $t .= "      default: false" . "\r\n";
        $t .= "    }" . "\r\n";
        $t .= "  }," . "\r\n";
        $t .= "  setup(props, { emit }) {" . "\r\n";
        $t .= "    const entity = reactive(props.value);" . "\r\n";
        $t .= "    const close = () => {" . "\r\n";
        $t .= "      emit(\"close\");" . "\r\n";
        $t .= "    };" . "\r\n";
        $t .= "    const onSave = () => {" . "\r\n";
        $t .= "      emit(\"save\", entity);" . "\r\n";
        $t .= "    };" . "\r\n";
        $t .= "    return {" . "\r\n";
        $t .= "      entity," . "\r\n";
        $t .= "      close," . "\r\n";
        $t .= "      onSave" . "\r\n";
        $t .= "    };" . "\r\n";
        $t .= "  }" . "\r\n";
        $t .= "});" . "\r\n";
        $t .= "</script>" . "\r\n";

        fwrite($objFopen, $t);
        fclose($objFopen);
//        }
    }

    private function createFrontendFile(AppTable $appTable, $frontendPath)
    {
        //service
        $objFopen = fopen($frontendPath . '/' . $this->appTableModuleName . 'Service.js', 'w');
        $t = "import Service from './Service';" . "\r\n";
        $t .= "" . "\r\n";
        $t .= "class " . $this->appTableModuleName . "Service extends Service {" . "\r\n";
        $t .= "    constructor() {" . "\r\n";
        $t .= "        super();" . "\r\n";
        $t .= "    }" . "\r\n";
        $t .= "    async get(pageParam) {" . "\r\n";
        $t .= "        return this.callApiGet(`/" . AppUtil::genModuleNameFormat($appTable->app_table_name) . "$" . "{pageParam}`);" . "\r\n";
        $t .= "    }" . "\r\n";
        $t .= "    async create(postData) {" . "\r\n";
        $t .= "        return this.callApiPost(`/" . AppUtil::genModuleNameFormat($appTable->app_table_name) . "`, postData);" . "\r\n";
        $t .= "    }" . "\r\n";
        $t .= "    async update(postData) {" . "\r\n";
        $t .= "        return this.callApiPut(`/" . AppUtil::genModuleNameFormat($appTable->app_table_name) . "`, postData);" . "\r\n";
        $t .= "    }" . "\r\n";
        $t .= "    async delete(id) {" . "\r\n";
        $t .= "        return this.callApiDelete(`/" . AppUtil::genModuleNameFormat($appTable->app_table_name) . "?_ids=$" . "{id}`);" . "\r\n";
        $t .= "    }" . "\r\n";
        $t .= "}" . "\r\n";
        $t .= "export default " . $this->appTableModuleName . "Service" . "\r\n";
        $t .= "/* route.js" . "\r\n";
        $t .= "            {" . "\r\n";
        $t .= "              path: \"" . AppUtil::genComponentNameFormat($appTable->app_table_name) . "\"," . "\r\n";
        $t .= "              name: \"app-" . AppUtil::genComponentNameFormat($appTable->app_table_name) . "\"," . "\r\n";
        $t .= "              component: () => import(\"@/views/App/pages/app/$this->appTableModuleName\")," . "\r\n";
        $t .= "              meta: {" . "\r\n";
        $t .= "                breadcrumb: [" . "\r\n";
        $t .= "                  { text: \"nav.dashboard\", href: \"/\", disabled: false }," . "\r\n";
        $t .= "                  { text: \"model.$appTable->app_table_name.$appTable->app_table_name\", href: \"\", disabled: true }" . "\r\n";
        $t .= "                ]," . "\r\n";
        $t .= "                pageTitle: { text: \"model.$appTable->app_table_name.$appTable->app_table_name\", icon: \"mdi-api\"}" . "\r\n";
        $t .= "              }" . "\r\n";
        $t .= "            }," . "\r\n";
        $t .= "*/" . "\r\n";
        $t .= "" . "\r\n";
        $t .= "/* UseMenuApi.js" . "\r\n";
        $t .= "        {" . "\r\n";
        $t .= "          title: \"model.$appTable->app_table_name.$appTable->app_table_name\"," . "\r\n";
        $t .= "          icon: \"mdi-file-outline\"," . "\r\n";
        $t .= "          to: \"/app/" . AppUtil::genComponentNameFormat($appTable->app_table_name) . "\"" . "\r\n";
        $t .= "         }" . "\r\n";
        $t .= "*/" . "\r\n";

        fwrite($objFopen, $t);
        fclose($objFopen);
    }

    private function createMsgFileApi(AppTable $appTable)
    {
        $msg = new \stdClass();
        $child = new \stdClass();
        foreach ($this->appTableColunmMetaData as $colunmMeta) {
            $child->{$colunmMeta['Field']} = $colunmMeta['Field'];
        }
        $msg->{$appTable->app_table_name} = $child;

        return $msg;
    }

    private function createMsgFile(AppTable $appTable)
    {

        $msgString = ",<br>";
        $msgString .= "\"" . $appTable->app_table_name . "\": {" . "<br>";
        $msgString .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"" . $appTable->app_table_name . "\": \"" . $appTable->app_table_name . "\"," . "<br>";
        $noOfColumn = count($this->appTableColunmMetaData);
        $i = 0;
        foreach ($this->appTableColunmMetaData as $colunmMeta) {
            $i = $i + 1;
//            if (!in_array($colunmMeta['Field'], $this->appTableBaseField)) {
            $msgString .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"" . $colunmMeta['Field'] . "\": \"" . $colunmMeta['Field'] . "\"," . "<br>";
//            }
        }
        $msgString .= "}" . "<br>";
        return $msgString;

        // load the data and delete the line from the array
//        $filename=$this->msgPath;
//        $fp = fopen($filename, 'r+');
//        $pos = filesize($filename);
//        while ($pos > 0) {
//            $pos = max($pos - 1024, 0);
//            fseek($fp, $pos);
//            $tmp = fread($fp, 1024);
//            $tmppos = strrpos($tmp, "\n");
//            if ($tmppos !== false) {
//                ftruncate($fp, $pos + $tmppos);
//                break;
//            }
//        }
//        fclose($fp);

//        $objFopen = fopen($this->msgPath, 'a');//'w' replace all file of old file ; 'a'  write the end of old file http://php.net/manual/en/function.fopen.php
//        $t = ""."\r\n";
//        $t .="    /*". " \r\n";
//        $t .="    |--------------------------------------------------------------------------". " \r\n";
//        $t .="    | ".$appTable->app_table_name." \r\n";
//        $t .="    |--------------------------------------------------------------------------". " \r\n";
//        $t .="    */". " \r\n";
//        $t .="    'model_".$appTable->app_table_name."' => '".$appTable->app_table_name."',". " \r\n";
//        foreach($this->appTableColunm as $field) {
//            $t .= "    'model_".$appTable->app_table_name."_".$field."' => '".$field."'," . " \r\n";
//        }
//        $t .= ");";
//        fwrite($objFopen, $t);
//        fclose($objFopen);

    }

    private function createRouteFileApi(AppTable $appTable, $haveCrudPermission = true)
    {
        $t = "Route::get(['AuthApi'], '" . $this->appTableModuleSubName . "', '" . $this->appTableModuleName . "Controller', 'crudList'," . ($haveCrudPermission ? "'".$appTable->app_table_name . '_list'."'" : 'null') . ");";
        $t .= "Route::post(['AuthApi'], '" . $this->appTableModuleSubName . "', '" . $this->appTableModuleName . "Controller', 'crudAdd'," . ($haveCrudPermission ? "'".$appTable->app_table_name . '_add'."'" : 'null') . ");";
        $t .= "Route::get(['AuthApi'], '" . $this->appTableModuleSubName . "ReadSingle', '" . $this->appTableModuleName . "Controller', 'crudReadSingle'," . ($haveCrudPermission ? "'".$appTable->app_table_name . '_view'."'" : 'null') . ");";
        $t .= "Route::put(['AuthApi'], '" . $this->appTableModuleSubName . "', '" . $this->appTableModuleName . "Controller', 'crudEdit'," . ($haveCrudPermission ? "'".$appTable->app_table_name . '_edit'."'" : 'null') . ");";
        $t .= "Route::delete(['AuthApi'], '" . $this->appTableModuleSubName . "', '" . $this->appTableModuleName . "Controller', 'crudDelete'," . ($haveCrudPermission ? "'".$appTable->app_table_name . '_delete'."'" : 'null') . ");";
        return $t;
    }

    private function createRouteFile(AppTable $appTable)
    {


//        $objFopen = fopen($this->routePath, 'a');//'w' replace all file of old file ; 'a'  write the end of old file http://php.net/manual/en/function.fopen.php
//        $t = ""."\r\n";
//        $t .="/*". " \r\n";
//        $t .="|--------------------------------------------------------------------------". " \r\n";
//        $t .="| ".$this->appTableModuleName."Controller \r\n";
//        $t .="|--------------------------------------------------------------------------". " \r\n";
//        $t .="*/". " \r\n";
//        $t .= "Route::get(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."list\",\"".$this->appTableModuleName."\",\"crudList\",\"".$appTable->app_table_name."_list\");"."\r\n";
//        $t .= "Route::get(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."add\",\"".$this->appTableModuleName."\",\"crudAdd\",\"".$appTable->app_table_name."_add\");"."\r\n";
//        $t .= "Route::post(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."add\",\"".$this->appTableModuleName."\",\"crudAddProcess\",\"".$appTable->app_table_name."_add\");"."\r\n";
//        $t .= "Route::get(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."edit\",\"".$this->appTableModuleName."\",\"crudEdit\",\"".$appTable->app_table_name."_edit\");"."\r\n";
//        $t .= "Route::post(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."edit\",\"".$this->appTableModuleName."\",\"crudEditProcess\",\"".$appTable->app_table_name."_edit\");"."\r\n";
//        $t .= "Route::post(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."delete\",\"".$this->appTableModuleName."\",\"crudDelete\",\"".$appTable->app_table_name."_delete\");";
//        fwrite($objFopen, $t);
//        fclose($objFopen);

        $t = "/*" . " <br>";
        $t .= "|--------------------------------------------------------------------------" . " <br>";
        $t .= "| " . $this->appTableModuleName . "Controller <br>";
        $t .= "|--------------------------------------------------------------------------" . " <br>";
        $t .= "*/" . " <br>";
        $t .= "Route::get(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudList\",\"" . $appTable->app_table_name . "_list\");" . "<br>";
        $t .= "Route::post(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudAdd\",\"" . $appTable->app_table_name . "_add\");" . "<br>";
        $t .= "Route::get(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "ReadSingle\",\"" . $this->appTableModuleName . "Controller\",\"crudReadSingle\",\"" . $appTable->app_table_name . "_view\");" . "<br>";
        $t .= "Route::put(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudEdit\",\"" . $appTable->app_table_name . "_edit\");" . "<br>";
        $t .= "Route::delete(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudDelete\",\"" . $appTable->app_table_name . "_delete\");";
        return $t;
    }

    public function crudEdit()
    {
        $id = FilterUtil::validateGetInt(ControllerUtils::encodeParamId(AppTable::$tableName));
        if (AppUtils::isEmpty($id)) {
            ControllerUtils::f404Static();
        }
        $appTable = $this->appTableService->findById($id);
        if (!$appTable) {
            ControllerUtils::f404Static();
        }

        $this->metaTitle = $appTable->app_table_name;
        $this->metaDescription = $appTable->app_table_name;
        $this->metaKeyword = $appTable->app_table_name;


        $this->pushDataToView['appTable'] = $appTable;
        $this->loadView($this->APP_TABLE_ADD_VIEW, $this->pushDataToView);
    }

    public function crudEditProcess()
    {
        $id = FilterUtil::validateGetInt(ControllerUtils::encodeParamId(AppTable::$tableName));
        if (AppUtils::isEmpty($id)) {
            ControllerUtils::f404Static();
        }
        $appTable = new AppTable();
        $appTable->populatePostData();
        $appTable->setId($id);

        $validator = new AppTableValidator($appTable);
        $errors = $validator->getValidationErrors();
        if ($errors) {
            $this->pushDataToView['validateErrors'] = $errors;
            $this->pushDataToView['appUserRole'] = $appTable;
            $this->loadView($this->APP_TABLE_ADD_VIEW, $this->pushDataToView);
        } else {
            $data_where['id'] = $appTable->getId();
            $effectRow = $this->appTableService->updateByObject($appTable, $data_where);
            ControllerUtils::setSuccessMessage('update state = ' . $effectRow);
            v_rediect('apptablelist');
        }
    }

    public function crudDelete()
    {
        $id = FilterUtil::validateGetInt(ControllerUtils::encodeParamId(AppTable::$tableName));
        if (AppUtils::isEmpty($id)) {
            ControllerUtils::f404Static();
        }
        $appTable = $this->appTableService->findById($id);
        if (!$appTable) {
            ControllerUtils::f404Static();
        }
        $themePath = "";
        if (!empty($appTable->getVtheme())) {
            $themePath = $appTable->getVtheme() . "/" . $appTable->app_table_name . "/";
        }


        //fix path for all rile to want delete
        $this->appTableModuleName = AppUtils::genPublicMethodName($appTable->app_table_name);

        $this->modelPath = __SITE_PATH . '/application/model/' . $this->appTableModuleName . '.php';
        $this->serviceInterfacePath = __SITE_PATH . '/application/serviceInterface/' . $this->appTableModuleName . 'ServiceInterface.php';
        $this->servicePath = __SITE_PATH . '/application/service/' . $this->appTableModuleName . 'Service.php';
        $this->validatorPath = __SITE_PATH . '/application/validator/' . $this->appTableModuleName . 'Validator.php';
        $this->controllerlPath = __SITE_PATH . '/application/controller/' . $this->appTableModuleName . 'Controller.php';
        $this->listPath = __SITE_PATH . '/application/views/' . $themePath . AppUtils::genModuleNameFormat($appTable->app_table_name);
        $this->viewPath = __SITE_PATH . '/application/views/' . $themePath . AppUtils::genModuleNameFormat($appTable->app_table_name);


        AppUtils::doDelfileFromPath($this->modelPath);
        AppUtils::doDelfileFromPath($this->serviceInterfacePath);
        AppUtils::doDelfileFromPath($this->servicePath);
        AppUtils::doDelfileFromPath($this->validatorPath);
        AppUtils::doDelfileFromPath($this->controllerlPath);
        AppUtils::doDelfileFromPath($this->listPath . 'List.php');
        AppUtils::doDelfileFromPath($this->viewPath . '.php');
        AppUtils::deleteFolder($this->listPath);
        AppUtils::deleteFolder($this->viewPath);

        //delete permission and role permission
        $permissionList = $this->appPermissionService->findPermissionListByTableName($appTable->app_table_name);
        if ($permissionList) {
            foreach ($permissionList as $permission) {
                $permissionId = $permission['id'];
                //delete app_permission_role by permission
                $this->appPermissionService->deletePermissionRoleByPermission($permissionId);
                //delete app_permission by permission id
                $this->appPermissionService->deleteById($permissionId);
            }
        }
        //delete record from app_table
        $effectRow = $this->appTableService->deleteById($id);
        //delete permisstion
        if ($effectRow) {
            //drop table after everything deleted
            $this->appTableService->dropTable($appTable->app_table_name);
        }
        if ($effectRow) {
            //return tr id for remove from table list
            $jason_return["hide_tr"] = "#hide_tr_" . $id;
            $jason_return["status_id"] = 1;
        } else {
            $jason_return["hide_tr"] = "";
            $jason_return["status_id"] = 0;
        }
        echo json_encode($jason_return);
        //ControllerUtils::setSuccessMessage('Delete state = '.$effectRow);
        //v_rediect('apptablelist');
    }
}