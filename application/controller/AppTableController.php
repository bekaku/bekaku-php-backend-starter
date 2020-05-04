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

            $this->postTheme = FilterUtil::filterPostString('vtheme');
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

            if ($isCreateList) {
                $this->listPath = __SITE_PATH . '/application/views/' . $this->postTheme . '/' . $this->appTableName . '/' . $this->appTableModuleSubName . 'List.php';
                //create folder if it doesn't already exist
                if (!file_exists(__SITE_PATH . '/application/views/' . $this->postTheme . '/' . $this->appTableName)) {
                    mkdir(__SITE_PATH . '/application/views/' . $this->postTheme . '/' . $this->appTableName, 0777, true);
                }
                $this->createListFile($appTable);
            }
            if ($isCreateView) {
                $this->viewPath = __SITE_PATH . '/application/views/' . $this->postTheme . '/' . $this->appTableName . '/' . $this->appTableModuleSubName . '.php';
//                $this->createViewFile($appTable);
            }

            if ($isCreateMsg) {
//                $this->msgPath =  __SITE_PATH.'/resources/lang/th/model.php';
                $msgJsonGen = $this->createMsgFile($appTable);
            }
            if ($isCreateRoute) {
                $this->routePath = __SITE_PATH . '/application/core/initRoutes.php';
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

            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "namespace application\\model;" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "use application\\core\\BaseModel;" . "\r\n";
            $strText .= "class " . $this->appTableModuleName . " extends BaseModel" . "\r\n";
            $strText .= "{" . "\r\n";

            /* attribute */
            $strText .= "    public static $" . "tableName = '" . $appTable->app_table_name . "';" . "\r\n";

            /* __construct */
            $strText .= "    public function __construct(\stdClass $" . "jsonData = null, $" . "uid = null, $" . "isUpdate = false)" . "\r\n";
            $strText .= "    {" . " \r\n";

            if (!$this->isRequireAuditInfo) {
                $strText .= "       //not use audit info" . " \r\n";
                $strText .= "        $" . "this->setAuditInfo(false);" . " \r\n";
                $strText .= "" . " \r\n";
            }

            /* init data type for field*/
            $strText .= "        /* init data type for field*/" . "\r\n";
            $strText .= "        $" . "this->setTableField(array(" . "\r\n";
            foreach ($this->appTableColunmMetaData as $colunmMeta) {

                if ($colunmMeta['Field'] != 'status') {
                    $strText .= "            '" . $colunmMeta['Field'] . "' => " . BaseModel::getColunmTypeStringByMysqlType($colunmMeta['vType'], $colunmMeta['Extra']) . "," . "\r\n";
                } else {
                    $strText .= "            'status' => self::TYPE_BOOLEAN," . "\r\n";
                }
            }
            $strText .= "        ));" . " \r\n";

            $strText .= "" . " \r\n";

            /* init data type for field use in update mode*/
            $strText .= "        /* init data type for field use in update mode*/" . "\r\n";
            $strText .= "        $" . "this->setTableFieldForEdit(array(" . "\r\n";

            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                if (!in_array($colunmMeta['Field'], $this->appTableBaseField)) {
                    if ($colunmMeta['Field'] != 'status') {
                        $strText .= "            '" . $colunmMeta['Field'] . "' => " . BaseModel::getColunmTypeStringByMysqlType($colunmMeta['vType'], $colunmMeta['Extra']) . "," . "\r\n";
                    } else {
                        $strText .= "            'status' => self::TYPE_BOOLEAN," . "\r\n";
                    }

                }
            }
            if ($this->isRequireAuditInfo) {
                //audit updated
                $strText .= "            'updated_user' => self::TYPE_INTEGER," . "\r\n";
                $strText .= "            'updated_date' => self::TYPE_DATE_TIME" . "\r\n";
            }

            $strText .= "        ));" . "\r\n";
            $strText .= "" . "\r\n";

            /* init optional field*/
            $strText .= "        /* init optional field*/" . "\r\n";
            $strText .= "        $" . "this->setTableOptionalField(array(" . "\r\n";
            $strText .= "            //'field_name_option'," . "\r\n";
            $strText .= "        ));" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "        $" . "this->populate($" . "jsonData, $" . "this, $" . "uid, $" . "isUpdate);" . "\r\n";
            $strText .= "    }" . "\r\n";
            $strText .= "" . "\r\n";

            /* getTableName */
            $strText .= "    public static function getTableName()" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        return self::$" . "tableName;" . "\r\n";
            $strText .= "    }" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "}";//end of file
            fwrite($objFopen, $strText);
            fclose($objFopen);
        }

    }

    private function createServiceInterfaceFile()
    {
        if ($this->isThisFileCanOverwrite($this->serviceInterfacePath)) {
            $objFopen = fopen($this->serviceInterfacePath, 'w');
            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "namespace application\\serviceInterface;" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "use application\\core\\AppBaseInterface;" . "\r\n";
            $strText .= "interface " . $this->appTableModuleName . "ServiceInterface extends AppBaseInterface" . "\r\n";
            $strText .= "{" . "\r\n";
            $strText .= "    //public function manualMethodList($" . "param);" . "\r\n";
            $strText .= "}";//end of file

            fwrite($objFopen, $strText);
            fclose($objFopen);
        }
    }

    private function createServiceFile(AppTable $appTable)
    {
        if ($this->isThisFileCanOverwrite($this->servicePath)) {
            $objFopen = fopen($this->servicePath, 'w');
            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "namespace application\\service;" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "use application\\core\\BaseDatabaseSupport;" . "\r\n";
            $strText .= "use application\\serviceInterface\\" . $this->appTableModuleName . "ServiceInterface;" . "\r\n";
//            $strText .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $strText .= "class " . $this->appTableModuleName . "Service extends BaseDatabaseSupport implements " . $this->appTableModuleName . "ServiceInterface" . "\r\n";
            $strText .= "{" . "\r\n";

            $strText .= "    protected $" . "tableName = '" . $appTable->app_table_name . "';" . "\r\n";
            $strText .= "" . "\r\n";

            /* __construct */
            $strText .= "    public function __construct($" . "dbConn){" . "\r\n";
            $strText .= "        $" . "this->setDbh($" . "dbConn);" . "\r\n";
            $strText .= "    }" . "\r\n";

            /* findAll */
            $strText .= "    public function findAll($" . "perpage=0, $" . "q_parameter=array())" . "\r\n";
            $strText .= "    {" . "\r\n";
//        $strText .= "        $"."query = \"SELECT * FROM \".$"."this->tableName;"."\r\n";

            $strText .= "        //if have param" . "\r\n";
            $strText .= "        $" . "data_bind_where = null;" . "\r\n";
            $strText .= "" . "\r\n";

            $isFirst = false;
//            foreach ($this->appTableColunm as $field) {
//                if (!$isFirst) {
//                    $strText .= "        $" . "query = \"SELECT " . $appTable->app_table_name . ".`" . $field . "` AS `" . $field . "` \";" . "\r\n";
//                    $isFirst = true;
//                } else {
//                    $strText .= "        $" . "query .=\"," . $appTable->app_table_name . ".`" . $field . "` AS `" . $field . "` \";" . "\r\n";
//                }
//            }
            $strText .= "        $" . "query = \"SELECT *  \";" . "\r\n";

            $strText .= "" . "\r\n";
            $strText .= "        $" . "query .=\"FROM " . $appTable->app_table_name . " AS " . $appTable->app_table_name . " \";" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "		//default where query" . "\r\n";
            $strText .= "        $" . "query .=\" WHERE " . $appTable->app_table_name . ".`id` IS NOT NULL \";" . "\r\n";
            $strText .= "		//custom where query" . "\r\n";
            $strText .= "       //$" . "query .= \"WHERE " . $appTable->app_table_name . ".custom_field =:customParam \";" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        //gen additional query and sort order" . "\r\n";
            $strText .= "       $" . "additionalParam = $" . "this->genAdditionalParamAndWhereForListPage($" . "q_parameter, $" . "this->tableName);" . "\r\n";
            $strText .= "       if(!empty($" . "additionalParam)){" . "\r\n";
            $strText .= "           if(!empty($" . "additionalParam['additional_query'])){" . "\r\n";
            $strText .= "               $" . "query .= $" . "additionalParam['additional_query'];" . "\r\n";
            $strText .= "           }" . "\r\n";
            $strText .= "           if(!empty($" . "additionalParam['where_bind'])){" . "\r\n";
            $strText .= "               $" . "data_bind_where = $" . "additionalParam['where_bind'];" . "\r\n";
            $strText .= "           }" . "\r\n";
            $strText .= "       }" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "        //custom where paramiter" . "\r\n";
            $strText .= "       // $" . "data_bind_where['custom_field']=$" . "paramValue;" . "\r\n";
            $strText .= "       //end" . "\r\n";

            $strText .= "        //paging buider" . "\r\n";
            $strText .= "        if($" . "perpage>0){" . "\r\n";
            $strText .= "            $" . "query .= $" . "this->pagingHelper($" . "query, $" . "perpage, $" . "data_bind_where);" . "\r\n";
            $strText .= "        }" . "\r\n";

            $strText .= "        //regular query" . "\r\n";
            $strText .= "        $" . "this->query($" . "query);" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        //START BIND VALUE FOR REGULAR QUERY" . "\r\n";
            $strText .= "        //$" . "this->bind(\":q_name\", \"%\".$" . "q_parameter['q_name'].\"%\");//bind param for 'LIKE'" . "\r\n";
            $strText .= "	     //$" . "this->bind(\":q_name\", $" . "q_parameter['q_name']);//bind param for '='" . "\r\n";

            /*
            foreach($this->appTableColunm as $fieldBind) {
                $paramBind = 'q_' . $fieldBind;

                $strText .= "        if(isset($"."q_parameter['".$paramBind."']) && $"."q_parameter['".$paramBind."']!=''){" . "\r\n";
                $strText .= "            $"."this->bind(\":".$paramBind."\", \"%\".$"."q_parameter['".$paramBind."'].\"%\");" . "\r\n";
                $strText .= "        }" . "\r\n";
            }
            */
            $strText .= "        //END BIND VALUE FOR REGULAR QUERY" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "        //bind param for search param" . "\r\n";
            $strText .= "        $" . "this->genBindParamAndWhereForListPage($" . "data_bind_where);" . "\r\n";
            $strText .= "" . "\r\n";

//            $strText .= "        //Return as Object Class" . "\r\n";
//            $strText .= "        /*" . "\r\n";
//            $strText .= "        $" . "resaultList =  $" . "this->resultset();" . "\r\n";
//            $strText .= "		if (is_array($" . "resaultList) || is_object($" . "resaultList)){" . "\r\n";
//            $strText .= "            $" . "findList = array();" . "\r\n";
//            $strText .= "            foreach($" . "resaultList as $" . "obj){" . "\r\n";
//            $strText .= "                $" . "singleObj = null;" . "\r\n";
//            $strText .= "                $" . "singleObj = new " . $this->appTableModuleName . "($" . "obj);" . "\r\n";
//            $strText .= "                array_push($" . "findList, $" . "singleObj);" . "\r\n";
//            $strText .= "            }" . "\r\n";
//            $strText .= "            return $" . "findList;" . "\r\n";
//            $strText .= "        }" . "\r\n";
//            $strText .= "        */" . "\r\n";


            $strText .= "        return  $" . "this->list();" . "\r\n";
            $strText .= "    }" . "\r\n";
            $strText .= "" . "\r\n";

            /* findById */
            $strText .= "    public function findById($" . "id)" . "\r\n";
            $strText .= "    {" . "\r\n";
//        $strText .= "        $"."query = \"SELECT * FROM \".$"."this->tableName.\" WHERE id=:id\";"."\r\n";
            $isFirst = false;
//            foreach ($this->appTableColunm as $findByField) {
//                if (!$isFirst) {
//                    $strText .= "        $" . "query = \"SELECT " . $appTable->app_table_name . ".`" . $findByField . "` AS `" . $findByField . "` \";" . "\r\n";
//                    $isFirst = true;
//                } else {
//                    $strText .= "        $" . "query .=\"," . $appTable->app_table_name . ".`" . $findByField . "` AS `" . $findByField . "` \";" . "\r\n";
//                }
//            }
            $strText .= "        $" . "query = \"SELECT *  \";" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        $" . "query .=\"FROM " . $appTable->app_table_name . " AS " . $appTable->app_table_name . " \";" . "\r\n";
            $strText .= "        $" . "query .=\"WHERE " . $appTable->app_table_name . ".`id`=:id \";" . "\r\n";
            $strText .= "" . "\r\n";


            $strText .= "        $" . "this->query($" . "query);" . "\r\n";
            $strText .= "        $" . "this->bind(\":id\", (int)$" . "id);" . "\r\n";


//            $strText .= "        //Return as Object Class" . "\r\n";
//            $strText .= "        /*" . "\r\n";
//            $strText .= "        $" . "result =  $" . "this->single();" . "\r\n";
//            $strText .= "		if (is_array($" . "result) || is_object($" . "result)){" . "\r\n";
//            $strText .= "            $" . $this->appTableModuleSubName . " = new " . $this->appTableModuleName . "($" . "result);" . "\r\n";
//            $strText .= "            return $" . "$this->appTableModuleSubName;" . "\r\n";
//            $strText .= "        }" . "\r\n";
//            $strText .= "        */" . "\r\n";


            $strText .= "        return  $" . "this->single();" . "\r\n";
            $strText .= "    }" . "\r\n";

            /* deleteById */
            $strText .= "    public function deleteById($" . "id)" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "query = \"DELETE FROM \".$" . "this->tableName.\" WHERE id=:id\";" . "\r\n";
            $strText .= "        $" . "this->query($" . "query);" . "\r\n";
            $strText .= "        $" . "this->bind(\":id\", (int)$" . "id);" . "\r\n";
            $strText .= "        return $" . "this->execute();" . "\r\n";
            $strText .= "    }" . "\r\n";

            /* createByArray */
            $strText .= "    public function createByArray($" . "data_array)" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        return $" . "this->insertHelper($" . "this->tableName, $" . "data_array);" . "\r\n";
            $strText .= "    }" . "\r\n";

            /* createByObject */
            $strText .= "    public function createByObject($" . "oject)" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        return $" . "this->insertObjectHelper($" . "oject);" . "\r\n";
            $strText .= "    }" . "\r\n";

            /* update */
            $strText .= "    public function update($" . "data_array, $" . "where_array, $" . "whereType = 'AND')" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        return $" . "this->updateHelper($" . "this->tableName, $" . "data_array, $" . "where_array, $" . "whereType);" . "\r\n";
            $strText .= "    }" . "\r\n";

            /* updateByObject */
            $strText .= "    public function updateByObject($" . "object, $" . "where_array, $" . "whereType = 'AND')" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        return $" . "this->updateObjectHelper($" . "object, $" . "where_array, $" . "whereType);" . "\r\n";
            $strText .= "    }" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "}";//end of file

            fwrite($objFopen, $strText);
            fclose($objFopen);
        }
    }

    private function createValidatorFile()
    {
        if ($this->isThisFileCanOverwrite($this->validatorPath)) {
            $objFopen = fopen($this->validatorPath, 'w');
            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "namespace application\\validator;" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "use application\\core\\BaseValidator;" . "\r\n";
            $strText .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $strText .= "class " . $this->appTableModuleName . "Validator extends BaseValidator" . "\r\n";
            $strText .= "{" . "\r\n";
            $strText .= "    public function __construct($this->appTableModuleName $" . $this->appTableModuleSubName . ")" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        //call parent construct" . "\r\n";
            $strText .= "        parent::__construct();" . "\r\n";
            $strText .= "        $" . "this->objToValidate = $" . $this->appTableModuleSubName . ";" . "\r\n";

            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                if (!in_array($colunmMeta['Field'], $this->appTableBaseField)) {

                    if ($colunmMeta['Null'] == 'NO') {
                        $strText .= "        $" . "this->validateField('" . $colunmMeta['Field'] . "', self::VALIDATE_REQUIRED);" . "\r\n";
                    }
                    $typeValidate = BaseValidator::getColunmValidatorByMysqlType($colunmMeta['vType']);
                    if (!AppUtils::isEmpty($typeValidate)) {
                        $strText .= "        $" . "this->validateField('" . $colunmMeta['Field'] . "', " . $typeValidate . ");" . "\r\n";
                    }
                }
            }
            $strText .= "" . "\r\n";
            $strText .= "        //Custom Validate" . "\r\n";
            $strText .= "        /*" . "\r\n";
            $strText .= "        if($" . $this->appTableModuleSubName . "->getPrice < $" . $this->appTableModuleSubName . "->getDiscount){" . "\r\n";
            $strText .= "          $" . "this->addError('price', 'Price Can't Must than Discount');" . "\r\n";
            $strText .= "        }" . "\r\n";
            $strText .= "        */" . "\r\n";

            $strText .= "    }" . "\r\n";

            $strText .= "}";//end of file

            fwrite($objFopen, $strText);
            fclose($objFopen);
        }
    }

    private function createControllerFile(AppTable $appTable)
    {
        if ($this->isThisFileCanOverwrite($this->controllerlPath)) {
            $objFopen = fopen($this->controllerlPath, 'w');
            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "/**" . "\r\n";
            $strText .= " * Created by Bekaku Php Back End System." . "\r\n";
            $strText .= " * Date: " . DateUtils::getDateNow(true) . "\r\n";
            $strText .= " */" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "namespace application\\controller;" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "use application\\core\\AppController;" . "\r\n";
            $strText .= "use application\\util\\FilterUtils;" . "\r\n";
            $strText .= "use application\\util\\i18next;" . "\r\n";
            $strText .= "use application\\util\\SystemConstant;" . "\r\n";
            $strText .= "use application\\util\\SecurityUtil;" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $strText .= "use application\\service\\" . $this->appTableModuleName . "Service " . ";" . "\r\n";
            $strText .= "use application\\validator\\" . $this->appTableModuleName . "Validator " . ";" . "\r\n";
            $strText .= "class " . $this->appTableModuleName . "Controller extends  AppController" . "\r\n";
            $strText .= "{" . "\r\n";
            $strText .= "    /**" . "\r\n";
            $strText .= "    * @var " . $this->appTableModuleName . "Service" . "\r\n";
            $strText .= "    */" . "\r\n";
            $strText .= "    private $" . $this->appTableModuleSubName . "Service;" . "\r\n";

            /*__construct*/
            $strText .= "    public function __construct($" . "databaseConnection)" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "this->setDbConn($" . "databaseConnection);" . "\r\n";
            $strText .= "        $" . "this->" . $this->appTableModuleSubName . "Service = new " . $this->appTableModuleName . "Service($" . "this->getDbConn());" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "    }" . "\r\n";

            /*__destruct*/
            $strText .= "    public function __destruct()" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "this->setDbConn(null);" . "\r\n";
            $strText .= "        unset($" . "this->" . $this->appTableModuleSubName . "Service);" . "\r\n";
            $strText .= "    }" . "\r\n";

            /*crudList*/
            $strText .= "    public function crudList()" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "perPage = FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) > 0 ? FilterUtils::filterGetInt(SystemConstant::PER_PAGE_ATT) : 0;" . "\r\n";
            $strText .= "        $" . "this->setRowPerPage($" . "perPage);" . "\r\n";
            $strText .= "        $" . "q_parameter = $" . "this->initSearchParam(new " . $this->appTableModuleName . "());" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse();" . "\r\n";
            $strText .= "        $" . "this->pushDataToView[SystemConstant::DATA_LIST_ATT] = $" . "this->" . $this->appTableModuleSubName . "Service->findAll($" . "this->getRowPerPage(), $" . "q_parameter);" . "\r\n";
            $strText .= "        $" . "this->pushDataToView[SystemConstant::APP_PAGINATION_ATT] = $" . "this->" . $this->appTableModuleSubName . "Service->getTotalPaging();" . "\r\n";
            $strText .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";

            $strText .= "    }" . "\r\n";
            //end crudList

            /*crudAdd*/
            $strText .= "    public function crudAdd()" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "uid = SecurityUtil::getAppuserIdFromJwtPayload();" . "\r\n";
            $strText .= "        $" . "jsonData = $" . "this->getJsonData(false);" . "\r\n";
            $strText .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(false);" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        if(!empty($" . "jsonData) && !empty($" . "uid)) {" . "\r\n";
            $strText .= "           $" . "entity = new " . $this->appTableModuleName . "($" . "jsonData, $" . "uid, false);" . "\r\n";
            $strText .= "           $" . "validator = new " . $this->appTableModuleName . "Validator($" . "entity);" . "\r\n";
            $strText .= "           if ($" . "validator->getValidationErrors()) {" . "\r\n";
            $strText .= "               jsonResponse($" . "this->setResponseStatus($" . "validator->getValidationErrors(), false, null), 400);" . "\r\n";
            $strText .= "           } else {" . "\r\n";
            $strText .= "               $" . "lastInsertId = $" . "this->" . $this->appTableModuleSubName . "Service->createByObject($" . "entity);" . "\r\n";
            $strText .= "               if ($" . "lastInsertId) {" . "\r\n";
            $strText .= "                   $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, true, i18next::getTranslation(('success.insert_succesfull')));" . "\r\n";
            $strText .= "                }" . "\r\n";
            $strText .= "           }" . "\r\n";
            $strText .= "        }" . "\r\n";
            $strText .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "    }" . "\r\n";
            //end crudAdd


            /*crudReadSingle*/
            $strText .= "    public function crudReadSingle()" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "id = FilterUtils::filterGetInt(SystemConstant::ID_PARAM);" . "\r\n";
            $strText .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(false);" . "\r\n";
            $strText .= "        $" . "item = null;" . "\r\n";
            $strText .= "        if ($" . "id > 0) {" . "\r\n";
            $strText .= "            $" . "item = $" . "this->" . $this->appTableModuleSubName . "Service->findById($" . "id);" . "\r\n";
            $strText .= "            if ($" . "item) {" . "\r\n";
            $strText .= "                $" . "this->pushDataToView = $" . "this->getDefaultResponse(true);" . "\r\n";
            $strText .= "            }" . "\r\n";
            $strText .= "        }" . "\r\n";
            $strText .= "        $" . "this->pushDataToView[SystemConstant::ENTITY_ATT] = $" . "item;" . "\r\n";
            $strText .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $strText .= "    }" . "\r\n";
            //end crudReadSingle

            /*crudEdit*/
            $strText .= "    public function crudEdit()" . "\r\n";
            $strText .= "    {" . "\r\n";

            $strText .= "        $" . "uid = SecurityUtil::getAppuserIdFromJwtPayload();" . "\r\n";
            $strText .= "        $" . "jsonData = $" . "this->getJsonData(false);" . "\r\n";
            $strText .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(false);" . "\r\n";
            $strText .= "		" . "\r\n";
            $strText .= "        if(!empty($" . "jsonData) && !empty($" . "uid)) {" . "\r\n";
            $strText .= "           $" . $this->appTableModuleSubName . " = new " . $this->appTableModuleName . "($" . "jsonData, $" . "uid, true);" . "\r\n";
            $strText .= "           $" . "validator = new " . $this->appTableModuleName . "Validator($" . $this->appTableModuleSubName . ");" . "\r\n";
            $strText .= "           if ($" . "validator->getValidationErrors()) {" . "\r\n";
            $strText .= "               jsonResponse($" . "this->setResponseStatus($" . "validator->getValidationErrors(), false, null), 400);" . "\r\n";
            $strText .= "           } else {" . "\r\n";
            $strText .= "                if (isset($" . $this->appTableModuleSubName . "->id)) {" . "\r\n";
            $strText .= "                   $" . "effectRow = $" . "this->" . $this->appTableModuleSubName . "Service->updateByObject($" . $this->appTableModuleSubName . ", array('id' => $" . $this->appTableModuleSubName . "->id));" . "\r\n";
            $strText .= "                   if ($" . "effectRow) {" . "\r\n";
            $strText .= "                       $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, true, i18next::getTranslation(('success.update_succesfull')));" . "\r\n";
            $strText .= "                   }" . "\r\n";
            $strText .= "               }" . "\r\n";
            $strText .= "           }" . "\r\n";
            $strText .= "       }" . "\r\n";
            $strText .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $strText .= "    }" . "\r\n";
            //end crudEditProcess


            /*crudDelete*/
            $strText .= "    public function crudDelete()" . "\r\n";
            $strText .= "    {" . "\r\n";
            $strText .= "        $" . "this->pushDataToView = $" . "this->getDefaultResponse(true);" . "\r\n";
            $strText .= "        $" . "idParams = FilterUtils::filterGetString(SystemConstant::ID_PARAMS);//paramiter format : idOfNo1_idOfNo2_idOfNo3_idOfNo4 ..." . "\r\n";
            $strText .= "        $" . "idArray = explode(SystemConstant::UNDER_SCORE, $" . "idParams);" . "\r\n";
            $strText .= "        if (count($" . "idArray) > 0) {" . "\r\n";
            $strText .= "            foreach ($" . "idArray AS $" . "id) {" . "\r\n";
            $strText .= "                $" . "entity = $" . "this->" . $this->appTableModuleSubName . "Service->findById($" . "id);" . "\r\n";
            $strText .= "                if ($" . "entity) {" . "\r\n";
            $strText .= "                    $" . "effectRow = $" . "this->" . $this->appTableModuleSubName . "Service->deleteById($" . "id);" . "\r\n";
            $strText .= "                    if (!$" . "effectRow) {" . "\r\n";
            $strText .= "                        $" . "this->pushDataToView = $" . "this->setResponseStatus($" . "this->pushDataToView, false, i18next::getTranslation('error.error_something_wrong'));" . "\r\n";
            $strText .= "                        break;" . "\r\n";
            $strText .= "                    }" . "\r\n";
            $strText .= "                }" . "\r\n";
            $strText .= "            }" . "\r\n";
            $strText .= "        }" . "\r\n";
            $strText .= "        jsonResponse($" . "this->pushDataToView);" . "\r\n";
            $strText .= "    }" . "\r\n";
            //end crudDelete

            $strText .= "" . "\r\n";
            $strText .= "}";//end of controller file

            fwrite($objFopen, $strText);
            fclose($objFopen);
        }
    }

    private function createListFile(AppTable $appTable)
    {

        if ($this->isThisFileCanOverwrite($this->listPath)) {
            $objFopen = fopen($this->listPath, 'w');
            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "use application\\model\\" . $this->appTableModuleName . ";" . "\r\n";
            $strText .= "use application\\util\\ControllerUtil;" . "\r\n";
            $strText .= "use application\\util\\FilterUtils;" . "\r\n";
            $strText .= "use application\\util\\i18next;" . "\r\n";
            $strText .= "use application\\util\\SystemConstant;" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "$" . $this->appTableModuleSubName . "List = (isset($" . "_V_DATA_TO_VIEW['" . $this->appTableModuleSubName . "List'])) ? $" . "_V_DATA_TO_VIEW['" . $this->appTableModuleSubName . "List'] : array();" . "\r\n";
            $strText .= "$" . "appPagination = (isset($" . "_V_DATA_TO_VIEW[SystemConstant::$" . "APP_PAGINATION_ATT])) ? $" . "_V_DATA_TO_VIEW[SystemConstant::$" . "APP_PAGINATION_ATT] : '';" . "\r\n";
            $strText .= "?>" . "\r\n";
            $strText .= "" . "\r\n";
            //page header
            $strText .= "<!-- Page-header start -->" . "\r\n";
            $strText .= "<div class=\"page-header card\">" . "\r\n";
            $strText .= "    <div class=\"row align-items-end\">" . "\r\n";
            $strText .= "        <div class=\"col-lg-8\">" . "\r\n";
            $strText .= "            <div class=\"page-header-title\">" . "\r\n";
            $strText .= "                <i class=\"icofont icofont-list bg-c-blue\"></i>" . "\r\n";
            $strText .= "                <div class=\"d-inline\">" . "\r\n";
            $strText .= "                    <h4><?= i18next::getTranslation('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "') ?></h4>" . "\r\n";
            $strText .= "                    <span><?= i18next::getTranslation('base.list') ?></span>" . "\r\n";
            $strText .= "                </div>" . "\r\n";
            $strText .= "            </div>" . "\r\n";
            $strText .= "        </div>" . "\r\n";
            $strText .= "        <div class=\"col-lg-4\">" . "\r\n";
            $strText .= "            <div class=\"page-header-breadcrumb\">" . "\r\n";
            $strText .= "                <ul class=\"breadcrumb-title\">" . "\r\n";
            $strText .= "                    <li class=\"breadcrumb-item\">" . "\r\n";
            $strText .= "                        <a href=\"<?=_BASEURL.'dashboard'?>\">" . "\r\n";
            $strText .= "                            <i class=\"icofont icofont-home\"></i>" . "\r\n";
            $strText .= "                        </a>" . "\r\n";
            $strText .= "                    </li>" . "\r\n";
            $strText .= "                    <li class=\"breadcrumb-item\"><a href=\"javascript:void(0)\"><?= i18next::getTranslation('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "') ?></a></li>" . "\r\n";
            $strText .= "                </ul>" . "\r\n";
            $strText .= "            </div>" . "\r\n";
            $strText .= "        </div>" . "\r\n";
            $strText .= "    </div>" . "\r\n";
            $strText .= "</div>" . "\r\n";
            $strText .= "<!-- Page-header end -->" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "<div class=\"page-body\">" . "\r\n";
            $strText .= "    <div class=\"row\">" . "\r\n";
            $strText .= "        <div class=\"col-sm-12\">" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "            <div class=\"card\">" . "\r\n";
            $strText .= "                <div class=\"card-header\">" . "\r\n";
            $strText .= "                    <div class=\"btn-group \" role=\"group\">" . "\r\n";
            $strText .= "                        <button type=\"button\" class=\"btn btn-primary btn-outline-primary app_open_searchbox\">" . "\r\n";
            $strText .= "                            <i class=\"icofont icofont-ui-search\"></i>" . "\r\n";
            $strText .= "                        </button>" . "\r\n";
            $strText .= "                        <a href=\"<?=_BASEURL.'" . AppUtils::getUrlFromTableName($appTable->app_table_name) . "add'?>\" class=\"btn btn-outline-primary\"><i class=\"icofont icofont-ui-add\"></i> <?= i18next::getTranslation('base.add_new') ?></a>" . "\r\n";
            $strText .= "                        <button type=\"button\" class=\"btn btn-danger btn-outline-danger app-delete-seleted-confirm\"" . "\r\n";
            $strText .= "                                app-parameter=\"<?=ControllerUtil::encodeParamId(" . $this->appTableModuleName . "::$" . "tableName)?>\" app-delete-all-url=\"<?=_BASEURL.'" . AppUtils::getUrlFromTableName($appTable->app_table_name) . "delete'?>\">" . "\r\n";
            $strText .= "                            <i class=\"icofont icofont-delete-alt\"></i> <?= i18next::getTranslation('base.delete_seleted') ?>" . "\r\n";
            $strText .= "                        </button>" . "\r\n";

            $strText .= "                    </div>" . "\r\n";
            $strText .= "                    <div class=\"card-header-right\">" . "\r\n";
            $strText .= "                        <ul class=\"list-unstyled card-option\" style=\"width: 35px;\">" . "\r\n";
            $strText .= "                            <li class=\"\"><i class=\"icofont icofont-simple-left\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-maximize full-card\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-minus minimize-card\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-refresh reload-card\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-error close-card\"></i></li>" . "\r\n";
            $strText .= "                        </ul>" . "\r\n";
            $strText .= "                    </div>" . "\r\n";
            $strText .= "                </div><!--end card-header-->" . "\r\n";
            $strText .= "                <div class=\"card-block\">" . "\r\n";
            $strText .= "" . "\r\n";
            //START SEARCH
            $strText .= "                    <!-- Start Search-->" . "\r\n";
            $strText .= "                    <div class=\"row\"  style='display: none;' id='app_search_holder'>" . "\r\n";
            $strText .= "                        <div class=\"col-md-12 v-bg-main-6\">" . "\r\n";
            $strText .= "                            <form role=\"form\" id=\"form_search\" method=\"get\">" . "\r\n";
            $strText .= "                                <div class=\"form-group row\">" . "\r\n";
            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                $fieldName = $colunmMeta['Field'];
                if (!in_array($fieldName, $this->appTableBaseField)) {
                    $strText .= "                                    <div class=\"col-md-4\">" . "\r\n";
                    $strText .= "                                        <label class=\"col-form-label\">&nbsp;</label>" . "\r\n";
                    $strText .= "                                        <input id=\"q_" . $fieldName . "\" name=\"q_" . $fieldName . "\" value=\"<?=FilterUtils::filterGetString('q_" . $fieldName . "')?>\" type=\"text\" " . "\r\n";
                    $strText .= "										class=\"form-control form-control-normal\" " . "\r\n";
                    $strText .= "										placeholder=\"<?= i18next::getTranslation('model." . $appTable->app_table_name . "." . $fieldName . "') ?>\">" . "\r\n";
                    $strText .= "                                    </div>" . "\r\n";
                }
            }

            $strText .= "                                </div>" . "\r\n";
            $strText .= "                                <div class=\"form-group row\">" . "\r\n";
            $strText .= "                                    <div class=\"col-sm-12\">" . "\r\n";
            $strText .= "                                        <button type=\"submit\" class=\"btn btn-primary m-b-0\"><i class=\"icofont icofont-search-alt-1\"></i> <?= i18next::getTranslation('base.search') ?></button>" . "\r\n";
            $strText .= "                                    </div>" . "\r\n";
            $strText .= "                                </div>" . "\r\n";
            $strText .= "                            </form>" . "\r\n";
            $strText .= "                        </div>" . "\r\n";
            $strText .= "                    </div>" . "\r\n";
            $strText .= "                    <!-- End Search-->" . "\r\n";
            //END SEARCH
            $strText .= "" . "\r\n";

            $strText .= "                    <div class=\"table-responsive\">" . "\r\n";
            $strText .= "                        <div class=\"table-content\">" . "\r\n";
            $strText .= "                            <table class=\"table table-striped dt-responsive nowrap\">" . "\r\n";
            $strText .= "                                <thead>" . "\r\n";
            $strText .= "                                <tr>" . "\r\n";
            $strText .= "                                    <th style=\"width:1%\">" . "\r\n";
            $strText .= "                                        <div class=\"checkbox-fade fade-in-primary\" data-toggle=\"tooltip\" data-placement=\"top\"" . "\r\n";
            $strText .= "                                             title=\"\" data-original-title=\"<?= i18next::getTranslation('base.select_all') ?>\">" . "\r\n";
            $strText .= "                                            <label>" . "\r\n";
            $strText .= "                                                <input type=\"checkbox\" name=\"checkBoxAll\" id=\"checkBoxAll\">" . "\r\n";
            $strText .= "                                                <span class=\"cr\">" . "\r\n";
            $strText .= "                                                      <i class=\"cr-icon icofont icofont-ui-check txt-primary\"></i>" . "\r\n";
            $strText .= "                                                    </span>" . "\r\n";
            $strText .= "                                                <span>&nbsp;</span>" . "\r\n";
            $strText .= "                                            </label>" . "\r\n";
            $strText .= "                                        </div>" . "\r\n";
            $strText .= "                                    </th>" . "\r\n";
            $strText .= "" . "\r\n";
            //table header
            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                $field = $colunmMeta['Field'];
                if (!in_array($field, $this->appTableBaseField)) {
                    if ($field != 'status') {

                        $strText .= "                                    <th style=\"vertical-align: middle;\">" . "\r\n";
                        $strText .= "                                        <a href=\"<" . "?=ControllerUtil::uriSortConcat('" . $field . "','ASC')?>\">" . "\r\n";
                        $strText .= "                                            <?= i18next::getTranslation('model." . $appTable->app_table_name . "." . $field . "') ?>" . "\r\n";
                        $strText .= "                                        </a>" . "\r\n";
                        $strText .= "                                        <span id=\"displaySort_" . $field . "\"></i></span>" . "\r\n";
                        $strText .= "                                    </th>" . "\r\n";
                    } else {
                        $strText .= "                                    <th style=\"vertical-align: middle;\"> <?= i18next::getTranslation('base.status') ?></th>" . "\r\n";
                    }
                }
            }

            $strText .= "                                    <th style=\"vertical-align: middle;\"><?= i18next::getTranslation('base.tool') ?></th>" . "\r\n";
            $strText .= "                                </tr>" . "\r\n";
            $strText .= "                                </thead>" . "\r\n";
            $strText .= "                                <tbody>" . "\r\n";

            $strText .= "                                <?php" . "\r\n";
            $strText .= "                                if($" . $this->appTableModuleSubName . "List) {" . "\r\n";
            $strText .= "                                foreach ($" . $this->appTableModuleSubName . "List AS $" . $this->appTableModuleSubName . ") {" . "\r\n";
            $strText .= "                                ?>" . "\r\n";

            $strText .= "                                    <tr id=\"hide_tr_<?=$" . $this->appTableModuleSubName . "->getId()?>\">" . "\r\n";
            $strText .= "                                        <td>" . "\r\n";
            $strText .= "                                            <div class=\"checkbox-fade fade-in-primary\">" . "\r\n";
            $strText .= "                                                <label>" . "\r\n";
            $strText .= "                                                    <input value=\"<?=$" . $this->appTableModuleSubName . "->getId()?>\" type=\"checkbox\" name=\"check\" id=\"checkbox1<?=$" . $this->appTableModuleSubName . "->getId()?>\">" . "\r\n";
            $strText .= "                                                    <span class=\"cr\">" . "\r\n";
            $strText .= "                                                      <i class=\"cr-icon icofont icofont-ui-check txt-primary\"></i>" . "\r\n";
            $strText .= "                                                    </span>" . "\r\n";
            $strText .= "                                                    <span>&nbsp;</span>" . "\r\n";
            $strText .= "                                                </label>" . "\r\n";
            $strText .= "                                            </div>" . "\r\n";
            $strText .= "                                        </td>" . "\r\n";

            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                $field = $colunmMeta['Field'];
                if (!in_array($field, $this->appTableBaseField)) {
                    if ($field == 'status') {
                        $strText .= "                                        <td >" . "\r\n";
                        $strText .= "                                            <p><?=($" . $this->appTableModuleSubName . "->isStatus()) ? \"<i class='fas fa-check-circle text-success'></i>\" : \"<i class='fas fa-check-circle text-danger'></i>\"?></p>" . "\r\n";
                        $strText .= "                                        </td>" . "\r\n";

                    } else if ($field == 'img_name') {
                        $strText .= "                                        <td >" . "\r\n";
                        $strText .= "                                            <p><img class=\"img-70 img-thumbnail\" src=\"<" . "?=$" . $this->appTableModuleSubName . "->getImgNameThumbnail()?>\" alt=\"<?=$" . $this->appTableModuleSubName . "->getId()?>\">" . "\r\n";
                        $strText .= "                                        </td>" . "\r\n";
                    } else {
                        $strText .= "                                        <td >" . "\r\n";
                        $strText .= "                                            <p><?=$" . $this->appTableModuleSubName . "->get" . AppUtils::genPublicMethodName($field) . "()?></p>" . "\r\n";
                        $strText .= "                                        </td>" . "\r\n";
                    }

                }
            }

            $strText .= "                                        <td class=\"action-icon\">" . "\r\n";
            $strText .= "                                            <a href=\"<?=_BASEURL.'" . AppUtils::getUrlFromTableName($appTable->app_table_name) . "edit?'.ControllerUtil::genParamId($" . $this->appTableModuleSubName . ")?>\" class=\"m-r-15 text-muted\" data-toggle=\"tooltip\"" . "\r\n";
            $strText .= "                                               data-placement=\"top\" title=\"\" data-original-title=\"<?=i18next::getTranslation('base.edit')?>\">" . "\r\n";
            $strText .= "                                                <i class=\"icofont icofont-ui-edit\"></i>" . "\r\n";
            $strText .= "                                            </a>" . "\r\n";
            $strText .= "                                            <a href=\"javascript:void(0)\" data-href=\"<?=_BASEURL.'" . AppUtils::getUrlFromTableName($appTable->app_table_name) . "delete?'.ControllerUtil::genParamId($" . $this->appTableModuleSubName . ")?>\"" . "\r\n";
            $strText .= "                                               data-id-hide=\"hide_tr_<?=$" . $this->appTableModuleSubName . "->getId()?>\"" . "\r\n";
            $strText .= "                                               class=\"app-delete-single-confirm\"" . "\r\n";
            $strText .= "                                               data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"<?=i18next::getTranslation('base.delete')?>\">" . "\r\n";
            $strText .= "                                                <i class=\"icofont icofont-delete-alt text-danger\"></i>" . "\r\n";
            $strText .= "                                            </a>" . "\r\n";
            $strText .= "                                        </td>" . "\r\n";
            $strText .= "                                    </tr>" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "                                    <?php" . "\r\n";
            $strText .= "                                    }" . "\r\n";
            $strText .= "                                }" . "\r\n";
            $strText .= "                                ?>" . "\r\n";

            $strText .= "                                </tbody>" . "\r\n";
            $strText .= "                            </table>" . "\r\n";
            $strText .= "                            <nav aria-label=\"...\">" . "\r\n";
            $strText .= "                                <?=$" . "appPagination;?>" . "\r\n";
            $strText .= "                            </nav>" . "\r\n";
            $strText .= "                        </div><!--end table-content-->" . "\r\n";
            $strText .= "                    </div><!--end table-responsive-->" . "\r\n";

            $strText .= "" . "\r\n";
            $strText .= "                </div><!--end card-block-->" . "\r\n";
            $strText .= "            </div><!--end card-->" . "\r\n";

            $strText .= "" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        </div><!--end col-sm-12-->" . "\r\n";
            $strText .= "    </div><!--end row-->" . "\r\n";
            $strText .= "</div><!--end page-body-->";
            //end of file


            fwrite($objFopen, $strText);
            fclose($objFopen);
        }

    }

    private function createViewFile(AppTable $appTable)
    {
        if ($this->isThisFileCanOverwrite($this->viewPath)) {
            $objFopen = fopen($this->viewPath, 'w');
            $strText = "<?php" . "\r\n";
            $strText .= $this->autoGenerateTextWarn . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "use application\\util\\FilterUtils;" . "\r\n";
            $strText .= "use application\\util\\i18next;" . "\r\n";
            $strText .= "use application\\util\\SystemConstant;" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "$" . "validateErrors = (isset($" . "_V_DATA_TO_VIEW[SystemConstant::" . "APP_VALIDATE_ERR_ATT])) ? $" . "_V_DATA_TO_VIEW[SystemConstant::$" . "APP_VALIDATE_ERR_ATT] : array();" . "\r\n";
            $strText .= "$" . $this->appTableModuleSubName . " = (isset($" . "_V_DATA_TO_VIEW['" . $this->appTableModuleSubName . "'])) ? $" . "_V_DATA_TO_VIEW['" . $this->appTableModuleSubName . "'] : array();" . "\r\n";
            $strText .= "?>" . "\r\n";

            //page header
            $strText .= "<!-- Page-header start -->" . "\r\n";
            $strText .= "<div class=\"page-header card\">" . "\r\n";
            $strText .= "    <div class=\"row align-items-end\">" . "\r\n";
            $strText .= "        <div class=\"col-lg-8\">" . "\r\n";
            $strText .= "            <div class=\"page-header-title\">" . "\r\n";
            $strText .= "                <i class=\"icofont icofont-file-code bg-c-blue\"></i>" . "\r\n";
            $strText .= "                <div class=\"d-inline\">" . "\r\n";
            $strText .= "                    <h4><?= i18next::getTranslation('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "') ?></h4>" . "\r\n";
            $strText .= "                    <span><?= i18next::getTranslation('base.form') ?></span>" . "\r\n";
            $strText .= "                </div>" . "\r\n";
            $strText .= "            </div>" . "\r\n";
            $strText .= "        </div>" . "\r\n";
            $strText .= "        <div class=\"col-lg-4\">" . "\r\n";
            $strText .= "            <div class=\"page-header-breadcrumb\">" . "\r\n";
            $strText .= "                <ul class=\"breadcrumb-title\">" . "\r\n";
            $strText .= "                    <li class=\"breadcrumb-item\">" . "\r\n";
            $strText .= "                        <a href=\"<?=_BASEURL.'dashboard'?>\">" . "\r\n";
            $strText .= "                            <i class=\"icofont icofont-home\"></i>" . "\r\n";
            $strText .= "                        </a>" . "\r\n";
            $strText .= "                    </li>" . "\r\n";
            $strText .= "                    <li class=\"breadcrumb-item\"><a href=\"<?=_BASEURL.'" . AppUtils::getUrlFromTableName($appTable->app_table_name) . "list'?>\"><?= i18next::getTranslation('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "') ?></a></li>" . "\r\n";
            $strText .= "                    <li class=\"breadcrumb-item\"><a href=\"javascript:void(0)\"><?= i18next::getTranslation('base.arr_form_page', array('page' => i18next::getTranslation('model." . $appTable->app_table_name . "." . $appTable->app_table_name . "'))) ?></a></li>" . "\r\n";
            $strText .= "                </ul>" . "\r\n";
            $strText .= "            </div>" . "\r\n";
            $strText .= "        </div>" . "\r\n";
            $strText .= "    </div>" . "\r\n";
            $strText .= "</div>" . "\r\n";
            $strText .= "<!-- Page-header end -->" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "<div class=\"page-body\">" . "\r\n";
            $strText .= "    <div class=\"row\">" . "\r\n";
            $strText .= "        <div class=\"col-sm-12\">" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "            <div class=\"card\">" . "\r\n";
            $strText .= "                <div class=\"card-header\">" . "\r\n";
            $strText .= "                    <div class=\"card-header-right\">" . "\r\n";
            $strText .= "                        <ul class=\"list-unstyled card-option\" style=\"width: 35px;\">" . "\r\n";
            $strText .= "                            <li class=\"\"><i class=\"icofont icofont-simple-left\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-maximize full-card\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-minus minimize-card\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-refresh reload-card\"></i></li>" . "\r\n";
            $strText .= "                            <li><i class=\"icofont icofont-error close-card\"></i></li>" . "\r\n";
            $strText .= "                        </ul>" . "\r\n";
            $strText .= "                    </div>" . "\r\n";
            $strText .= "                </div><!--end card-header-->" . "\r\n";
            $strText .= "                <div class=\"card-block\">" . "\r\n";
            $strText .= "" . "\r\n";

            $strText .= "                    <h4 class=\"sub-title\"><?=i18next::getTranslation('helper.text_require')?></h4>" . "\r\n";
            $strText .= "                    <form role=\"form\" id=\"form_search\" action=\"<?=FilterUtils::filterServerUrl('REQUEST_URI')?>\" enctype=\"multipart/form-data\" method=\"post\">" . "\r\n";
            foreach ($this->appTableColunmMetaData as $colunmMeta) {
                $field = $colunmMeta['Field'];
                if (!in_array($field, $this->appTableBaseField)) {
                    $requireSpan = "<span class='text-danger'>*</span>";
                    $limitTextLenght = "";
                    $typeDateClass = false;
                    $isTextArea = false;
                    if ($colunmMeta['Null'] == 'YES') {
                        $requireSpan = "";
                    }
                    if ($colunmMeta['vType'] == 'date') {
                        $typeDateClass = true;
                    }
                    if (!AppUtil::isEmpty($colunmMeta['vLength']) && $colunmMeta['vLength'] > 0) {
                        $limitTextLenght = "maxlength=\"" . $colunmMeta['vLength'] . "\"";
                        if ($colunmMeta['vLength'] >= 120) {
                            $isTextArea = true;
                        }
                    }


                    if ($field == 'img_name') {
                        $strText .= "						<div class=\"form-group row\">" . "\r\n";
                        $strText .= "                            <div class=\"col-sm-2\">" . "\r\n";
                        $strText .= "                            <img class=\"img-thumbnail\" src=\"<?=$" . $this->appTableModuleSubName . "->getImgNameThumbnail()?>\">" . "\r\n";
                        $strText .= "                            </div>" . "\r\n";
                        $strText .= "                            <div class=\"col-sm-10\">" . "\r\n";
                        $strText .= "                                <input type=\"file\" class=\"filer_input_single\" id=\"<?=SystemConstant::$" . "APP_IMAGE_FILE_UPLOAD_ATT?>\" name=\"<?=SystemConstant::$" . "APP_IMAGE_FILE_UPLOAD_ATT?>\" value=\"<?=i18next::getTranslation('base.img_choose')?>\" />" . "\r\n";

                        $strText .= "								<?php if($" . $this->appTableModuleSubName . "->getImgName()){?>" . "\r\n";
                        $strText .= "                                <div class=\"row\">" . "\r\n";
                        $strText .= "                                    <div class=\"col-sm-12\">" . "\r\n";
                        $strText .= "                                        <div class=\"checkbox-fade fade-in-danger\">" . "\r\n";
                        $strText .= "                                            <label>" . "\r\n";
                        $strText .= "                                                <input value=\"1\" type=\"checkbox\" name=\"<?=SystemConstant::$" . "APP_IMAGE_FILE_DELETE_ATT?>\" id=\"<?=SystemConstant::$" . "APP_IMAGE_FILE_DELETE_ATT?>\">" . "\r\n";
                        $strText .= "                                                <span class=\"cr\">" . "\r\n";
                        $strText .= "                                                    <i class=\"cr-icon icofont icofont-ui-check txt-danger\"></i>" . "\r\n";
                        $strText .= "                                                </span>" . "\r\n";
                        $strText .= "                                                <span><?=i18next::getTranslation('base.img_delete')?></span>" . "\r\n";
                        $strText .= "                                                <input type=\"hidden\" name=\"img_name\" id=\"img_name\" value=\"<?=$" . $this->appTableModuleSubName . "->getImgName();?>\" />" . "\r\n";
                        $strText .= "                                            </label>" . "\r\n";
                        $strText .= "                                        </div>" . "\r\n";
                        $strText .= "                                    </div>" . "\r\n";
                        $strText .= "                                </div>" . "\r\n";
                        $strText .= "                                <?php } ?>" . "\r\n";

                        $strText .= "                            </div>" . "\r\n";
                        $strText .= "                        </div>" . "\r\n";

                    } else if ($field == 'status') {
                        $strText .= "                        <div class=\"form-group row\">" . "\r\n";
                        $strText .= "                            <label class=\"col-sm-2 col-form-label\"><?=i18next::getTranslation('base.status')?></label>" . "\r\n";
                        $strText .= "                            <div class=\"col-sm-10\">" . "\r\n";
                        $strText .= "                                <select name=\"status\" id=\"status\" class=\"js-example-basic-single col-sm-12\">" . "\r\n";
                        $strText .= "                                        <option value=\"1\" <?=($" . $this->appTableModuleSubName . "->isStatus()) ? \"selected\" : \"\"?>><?=i18next::getTranslation('base.enable')?></option>" . "\r\n";
                        $strText .= "                                        <option value=\"0\" <?=(!$" . $this->appTableModuleSubName . "->isStatus()) ? \"selected\" : \"\"?>><?=i18next::getTranslation('base.disable')?></option>" . "\r\n";
                        $strText .= "                                </select>" . "\r\n";
                        $strText .= "                            </div>" . "\r\n";
                        $strText .= "                        </div>" . "\r\n";
                    } else {//Textbox form
                        $strText .= "                        <div class=\"form-group <?=(array_key_exists('" . $field . "', $" . "validateErrors)) ? \"has-danger\" : \"\"?> row\">" . "\r\n";
                        $strText .= "                            <label class=\"col-sm-2 col-form-label\" for=\"" . $field . "\"><?=i18next::getTranslation('model." . $appTable->app_table_name . "." . $field . "')?>" . $requireSpan . "</label>" . "\r\n";
                        $strText .= "                            <div class=\"col-sm-10\">" . "\r\n";

                        if ($isTextArea) {
                            $strText .= "                                <textarea name=\"" . $field . "\" id=\"" . $field . "\"" . "\r\n";
                            $strText .= "                                          class=\"form-control  max-textarea form-control-<?=(array_key_exists('" . $limitTextLenght . "', $" . "validateErrors)) ? \"danger\" : \"normal\"?>\"" . "\r\n";
                            $strText .= "                                          " . $limitTextLenght . " rows=\"4\"><?=$" . $this->appTableModuleSubName . "->get" . AppUtils::genPublicMethodName($field) . "()?></textarea>" . "\r\n";
                        } else if ($typeDateClass) {
                            $strText .= "                                <div class='input-group'>" . "\r\n";
                            $strText .= "                                <input type=\"text\" value=\"<?=$" . $this->appTableModuleSubName . "->get" . AppUtils::genPublicMethodName($field) . "()?>\" " . (($typeDateClass) ? "readonly" : "") . " name=\"" . $field . "\" id=\"" . $field . "\" class=\" " . (($typeDateClass) ? "app_datepicker" : "") . " form-control form-control-<?=(array_key_exists('" . $field . "', $" . "validateErrors)) ? \"danger\" : \"normal\"?>\" >" . "\r\n";
                            $strText .= "                                    <span class=\"input-group-addon \">" . "\r\n";
                            $strText .= "                                        <i class=\"icofont icofont-ui-calendar\"></i>" . "\r\n";
                            $strText .= "                                     </span>" . "\r\n";
                            $strText .= "                                </div>" . "\r\n";
                        } else {
                            $strText .= "                                <input " . $limitTextLenght . " type=\"text\" value=\"<?=$" . $this->appTableModuleSubName . "->get" . AppUtils::genPublicMethodName($field) . "()?>\" name=\"" . $field . "\" id=\"" . $field . "\" class=\"form-control form-control-<?=(array_key_exists('" . $field . "', $" . "validateErrors)) ? \"danger\" : \"normal\"?>\" >" . "\r\n";
                        }


                        $strText .= "                                <?=(array_key_exists('" . $field . "', $" . "validateErrors)) ? \" <div class='col-form-label'>\".$" . "validateErrors['" . $field . "'].\"</div>\" : \"\"?>" . "\r\n";
                        $strText .= "                            </div>" . "\r\n";
                        $strText .= "                        </div>" . "\r\n";
                    }
                }
            }

            $strText .= "" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "                        <div class=\"form-group row\">" . "\r\n";
            $strText .= "                            <div class=\"col-sm-12\">" . "\r\n";
            $strText .= "                                <button type=\"submit\" class=\"btn btn-success m-b-0\"><i class=\"ti-save\"></i> <?= i18next::getTranslation('base.save') ?></button>" . "\r\n";
            $strText .= "                                <a href=\"<?=_BASEURL.'" . AppUtils::getUrlFromTableName($appTable->app_table_name) . "list'?>\" class=\"btn m-b-0\"><i class=\"ti-back-right\"></i> <?= i18next::getTranslation('base.cancel') ?></a>" . "\r\n";
            $strText .= "                            </div>" . "\r\n";
            $strText .= "                        </div>" . "\r\n";
            $strText .= "                    </form>" . "\r\n";

            $strText .= "" . "\r\n";
            $strText .= "                </div><!--end card-block-->" . "\r\n";
            $strText .= "            </div><!--end card-->" . "\r\n";

            $strText .= "" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "" . "\r\n";
            $strText .= "        </div><!--end col-sm-12-->" . "\r\n";
            $strText .= "    </div><!--end row-->" . "\r\n";
            $strText .= "</div><!--end page-body-->";
            //end of file

            fwrite($objFopen, $strText);
            fclose($objFopen);
        }
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
            if (!in_array($colunmMeta['Field'], $this->appTableBaseField)) {
                $msgString .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"" . $colunmMeta['Field'] . "\": \"" . $colunmMeta['Field'] . "\"," . "<br>";
            }
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
//        $strText = ""."\r\n";
//        $strText .="    /*". " \r\n";
//        $strText .="    |--------------------------------------------------------------------------". " \r\n";
//        $strText .="    | ".$appTable->app_table_name." \r\n";
//        $strText .="    |--------------------------------------------------------------------------". " \r\n";
//        $strText .="    */". " \r\n";
//        $strText .="    'model_".$appTable->app_table_name."' => '".$appTable->app_table_name."',". " \r\n";
//        foreach($this->appTableColunm as $field) {
//            $strText .= "    'model_".$appTable->app_table_name."_".$field."' => '".$field."'," . " \r\n";
//        }
//        $strText .= ");";
//        fwrite($objFopen, $strText);
//        fclose($objFopen);

    }

    private function createRouteFile(AppTable $appTable)
    {


//        $objFopen = fopen($this->routePath, 'a');//'w' replace all file of old file ; 'a'  write the end of old file http://php.net/manual/en/function.fopen.php
//        $strText = ""."\r\n";
//        $strText .="/*". " \r\n";
//        $strText .="|--------------------------------------------------------------------------". " \r\n";
//        $strText .="| ".$this->appTableModuleName."Controller \r\n";
//        $strText .="|--------------------------------------------------------------------------". " \r\n";
//        $strText .="*/". " \r\n";
//        $strText .= "Route::get(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."list\",\"".$this->appTableModuleName."\",\"crudList\",\"".$appTable->app_table_name."_list\");"."\r\n";
//        $strText .= "Route::get(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."add\",\"".$this->appTableModuleName."\",\"crudAdd\",\"".$appTable->app_table_name."_add\");"."\r\n";
//        $strText .= "Route::post(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."add\",\"".$this->appTableModuleName."\",\"crudAddProcess\",\"".$appTable->app_table_name."_add\");"."\r\n";
//        $strText .= "Route::get(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."edit\",\"".$this->appTableModuleName."\",\"crudEdit\",\"".$appTable->app_table_name."_edit\");"."\r\n";
//        $strText .= "Route::post(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."edit\",\"".$this->appTableModuleName."\",\"crudEditProcess\",\"".$appTable->app_table_name."_edit\");"."\r\n";
//        $strText .= "Route::post(\"".AppUtils::getUrlFromTableName($appTable->app_table_name)."delete\",\"".$this->appTableModuleName."\",\"crudDelete\",\"".$appTable->app_table_name."_delete\");";
//        fwrite($objFopen, $strText);
//        fclose($objFopen);

        $strText = "/*" . " <br>";
        $strText .= "|--------------------------------------------------------------------------" . " <br>";
        $strText .= "| " . $this->appTableModuleName . "Controller <br>";
        $strText .= "|--------------------------------------------------------------------------" . " <br>";
        $strText .= "*/" . " <br>";
        $strText .= "Route::get(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudList\",\"" . $appTable->app_table_name . "_list\");" . "<br>";
        $strText .= "Route::post(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudAdd\",\"" . $appTable->app_table_name . "_add\");" . "<br>";
        $strText .= "Route::get(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "ReadSingle\",\"" . $this->appTableModuleName . "Controller\",\"crudReadSingle\",\"" . $appTable->app_table_name . "_view\");" . "<br>";
        $strText .= "Route::put(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudEdit\",\"" . $appTable->app_table_name . "_edit\");" . "<br>";
        $strText .= "Route::delete(['AuthApi','PermissionGrant'],\"" . $this->appTableModuleSubName . "\",\"" . $this->appTableModuleName . "Controller\",\"crudDelete\",\"" . $appTable->app_table_name . "_delete\");";
        return $strText;
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