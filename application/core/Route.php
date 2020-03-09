<?php namespace application\core;

use application\service\ApiClientService;
use application\service\AppUserService as AppUserService;
use application\service\AuthenService;
use application\service\LoginService as LoginService;
use application\util\AppUtil;
use application\util\AppUtil as AppUtils;
use application\util\ControllerUtil;
use application\util\ControllerUtil as ControllerUtils;
use application\util\FilterUtils as FilterUtil;
use application\util\i18next;
use application\util\MessageUtils;
use application\util\MessageUtils as MessageUtil;
use application\util\SecurityUtil;
use application\util\SystemConstant;
use Exception;


/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 24/12/2015
 * Time: 2:53 PM
 */
class Route
{
    //static value
    public static $METHOD_TYPE_ATT = "methodType";
    public static $URL_ATT = "url";
    public static $CONTROLLER_ATT = "controller";
    public static $ACTION_ATT = "action";
    public static $PERMISSION_ATT = "permission";

    public static $METHOD_TYPE_GET_VAL = "GET";//select
    public static $METHOD_TYPE_POST_VAL = "POST";//update
    public static $METHOD_TYPE_PUT_VAL = "PUT";//create
    public static $METHOD_TYPE_PATCH_VAL = "PATCH";//update
    public static $METHOD_TYPE_DELETE_VAL = "DELETE";//update

    public static $DEFAULT_URL_HOME = "public";//home dashboard
    public static $CONTROLLER_METHOD_INDEX = "index";

    public static $CONTROLLER_POST_FIX = 'Controller';

    public static $routeList = array();

    public static $methodType;
    public static $url;
    public static $controllerName;
    public static $actionName;
    public static $requestMethod;
    public static $permissionName;
    public static $paramToActionController = array();

    public static function get($url, $controller, $method, $permission = null)
    {

        $data[] = "";
        /*
                $data[Route::$METHOD_TYPE_ATT] = Route::$METHOD_TYPE_GET_VAL;
                $data[Route::$URL_ATT] = $url;
                $data[Route::$CONTROLLER_ATT] = $controller;
                $data[Route::$ACTION_ATT] = $method;
                $data[Route::$PERMISSION_ATT] = $permission;
                array_push(Route::$routeList, $data);
        */
        $data[self::$METHOD_TYPE_ATT] = self::$METHOD_TYPE_GET_VAL;
        $data[self::$URL_ATT] = $url;
        $data[self::$CONTROLLER_ATT] = $controller;
        $data[self::$ACTION_ATT] = $method;
        $data[self::$PERMISSION_ATT] = $permission;
        array_push(self::$routeList, $data);

    }

    public static function post($url, $controller, $method, $permission = null)
    {

        $data[] = "";

        $data[self::$METHOD_TYPE_ATT] = self::$METHOD_TYPE_POST_VAL;
        $data[self::$URL_ATT] = $url;
        $data[self::$CONTROLLER_ATT] = $controller;
        $data[self::$ACTION_ATT] = $method;
        $data[self::$PERMISSION_ATT] = $permission;
        array_push(self::$routeList, $data);

    }

    public static function put($url, $controller, $method, $permission = null)
    {

        $data[] = "";

        $data[self::$METHOD_TYPE_ATT] = self::$METHOD_TYPE_PUT_VAL;
        $data[self::$URL_ATT] = $url;
        $data[self::$CONTROLLER_ATT] = $controller;
        $data[self::$ACTION_ATT] = $method;
        $data[self::$PERMISSION_ATT] = $permission;
        array_push(self::$routeList, $data);

    }

    public static function route($url)
    {
        self::initProductionMode();
        //open database connection
        $databaseBase = new DatabaseBase();
        if ($databaseBase->getSystemConnection()) {

            self::$requestMethod = FilterUtil::filterServer('REQUEST_METHOD');
            /* check in normal url mode */
            $isUrlExit = self::isUrlExit($url);
            if ($isUrlExit) {
                self::controller($databaseBase->getSystemConnection());
            } else {
                ControllerUtils::f404Static('Error 404');
            }
        }
        /* Close database connection */
        if ($databaseBase->getSystemConnection()) {
            $databaseBase->closeConnection();
            unset($databaseBase);
        }

    }

    public static function controller($connection)
    {

        if (file_exists(__SITE_PATH . '/application/controller/' . self::$controllerName . self::$CONTROLLER_POST_FIX . '.php')) {

            try {
                $controllerName = '\\application\\controller\\' . self::$controllerName . self::$CONTROLLER_POST_FIX;
                require_once(__SITE_PATH . '/application/controller/' . self::$controllerName . self::$CONTROLLER_POST_FIX . '.php');
                $controllerClass = new $controllerName($connection);

//                if ($controllerClass->headerContentType!=SystemConstant::$CONTENT_TYPE_APPLICATION_JSON){
                header($controllerClass->headerContentType);
//                }


                //check if login require
                self::authorizationManage($controllerClass->isAuthRequired, $connection);


                if (!AppUtils::isArrayEmpty(self::$paramToActionController)) {
                    $controllerClass->{self::$actionName}(self::$paramToActionController);
                } else {
                    $controllerClass->{self::$actionName}();
                }

                unset($controllerClass);
            } catch (Exception $e) {
                ControllerUtils::f404Static();
            }
        } else {
            ControllerUtils::f404Static();
        }
    }

    private static function authorizationManage($isAuthRequired, $connection)
    {

        //check if login require
        if ($isAuthRequired) {

            $authorizationData = SecurityUtil::requiredTokenAuthorization(new AuthenService($connection), new ApiClientService($connection));
            $payLoad = $authorizationData['payload'];
            $locale = $payLoad['locale'] ? $payLoad['locale'] : ControllerUtil::getCurrentLocale();
            //set locale
            ControllerUtil::i18nextInit($locale);
            if (!AppUtils::isEmpty(self::$permissionName)) {
                if (!ControllerUtils::isPermissionByUserId($connection, $payLoad[SystemConstant::JWT_USER_ID_ATT], self::$permissionName)) {
                    ControllerUtils::displayError(i18next::getTranslation('error.permissionDeny'));
                }
            }
        } else {
            //get normal authorization
            //verify authoriztion by pass on developer mode test
            $isProductionMode = MessageUtils::getConfig('production_mode');
            if ($isProductionMode) {
                $jwt = SecurityUtil::decodeJWTAuthorizationData(SecurityUtil::getRequestHeaders(), new ApiClientService($connection));
                if (!$jwt[SystemConstant::SERVER_STATUS_ATT]) {
                    ControllerUtil::f401Static(null, $jwt);
                } else {
                    $payLoad = $jwt['payload'];
                    $locale = $payLoad['locale'] ? $payLoad['locale'] : ControllerUtil::getCurrentLocale();
                    //set locale
                    ControllerUtil::i18nextInit($locale);
                }
            } else {
                //set locale
                ControllerUtil::i18nextInit(ControllerUtil::getCurrentLocale());
            }
        }
    }

    /**
     * @param $url
     * @return bool
     */
    public static function isUrlExit($url)
    {

        if (!$url) {
            $url = self::$DEFAULT_URL_HOME;
        }
        $isUrlExit = false;

        $routeList = self::$routeList;
//        $routeList = require __SITE_PATH . "/application/configuration/routeListArray.php";

        /* check in normal url mode */
        foreach ($routeList as $route) {
            if ($route[self::$URL_ATT] == $url && $route[self::$METHOD_TYPE_ATT] == self::$requestMethod) {

                self::$methodType = $route[self::$METHOD_TYPE_ATT];
                self::$url = $route[self::$URL_ATT];
                self::$controllerName = $route[self::$CONTROLLER_ATT];
                self::$actionName = $route[self::$ACTION_ATT];
                self::$permissionName = $route[self::$PERMISSION_ATT];

                $isUrlExit = true;
                break;
            }
        }
        return $isUrlExit;
    }

    public static function initProductionMode()
    {
        $productionMode = MessageUtil::getConfig('production_mode');

        if (function_exists("set_time_limit") == true and @ini_get("safe_mode") == 0) {
            @set_time_limit(300);
        }

        if (!AppUtils::isEmpty($productionMode)) {

            switch ($productionMode) {
                case 'development':
                    error_reporting(E_ALL);
                    ini_set('display_errors', '1'); // if 1 show all error 0 don't show errow
                    break;
                case 'testing':
                case 'production':
                    error_reporting(0);
                    ini_set('display_errors', '0'); // if 1 show all error 0 don't show errow
                    break;
                default:
                    exit('The application environment is not set correctly.');
            }
        }

        @ini_set('magic_quotes_gpc', 'Off');
        @ini_set('register_globals', 'Off');
        @ini_set('default_charset', 'UTF-8');
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', '36000');
        @ini_set('upload_max_filesize', '999M');
        @ini_set('post_max_size', '999M');
        @ini_set('safe_mode', 'Off');
        @ini_set('mysql.connect_timeout', '20');
        @ini_set('session.auto_start', 'Off');
        @ini_set('session.use_only_cookies', 'On');
        @ini_set('session.use_cookies', 'On');
        @ini_set('session.use_trans_sid', 'Off');
        @ini_set('session.cookie_httponly', 'On');
        @ini_set('session.gc_maxlifetime', '3600');


//        @ini_set('allow_url_fopen', 'Off');
//        ini_set('display_errors', 'Off');
//        ini_set('error_reporting', 'Off');

    }
}