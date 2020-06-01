<?php
//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
//header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
|--------------------------------------------------------------------------
| auto load all service and serviceImpl php files in folder
|--------------------------------------------------------------------------
*/
spl_autoload_register(function($class) {
    $path = __SITE_PATH."/".str_replace('\\', '/', $class) . '.php';
    if(file_exists($path)){
        require_once  $path;
    }
});
require __SITE_PATH .'/application/util/common.php';
require __SITE_PATH.'/application/route/api.php';

use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\MessageUtils;

/*
|--------------------------------------------------------------------------
| Reuire Application Configuration
|--------------------------------------------------------------------------
*/
$configPath = get_env('PROJECT_DATA_HOME');
if (empty($configPath)) {
    ControllerUtil::displayError('Config file not found.');
}
$configPath .= "/configuration/app.php";
$configArray=null;
if (file_exists($configPath)) {
    $configArray = require $configPath;
}
define('__APP_CONFIG', $configArray);
/*
|--------------------------------------------------------------------------
| define useful variable for whole project
|--------------------------------------------------------------------------
*/
$url = AppUtil::getServerIp();

define('__SITEURL', AppUtil::getServerPort());
define('__BASEMODULE', MessageUtils::getConfig('base_module_name'));
$isUrlRewriting = MessageUtils::getConfig('url_rewriting');
if($isUrlRewriting){
    define('_BASEURL', MessageUtils::getConfig('url_rewriting_project_path'));
}else{
    define('_BASEURL', MessageUtils::getConfig('base_index_name')."?".MessageUtils::getConfig('base_module_name')."=");
}
define('__DOCUMENT_ROOT', MessageUtils::getConfig('url_rewriting_project_path'));
define('__RESOURCES', MessageUtils::getConfig('base_project_resources_path'));
define('__RESOURCES_FIXED', $url.":".MessageUtils::getConfig('url_port').__RESOURCES);//fixed bug for iframe
define('__RESOURCES_ASSETS', __RESOURCES.'/assets');

// Data Upload
define('__UPLOAD_PATH', MessageUtils::getConfig('base_data_path'));
define('__UPLOAD_PATH_IMG', MessageUtils::getConfig('base_data_path').'/img');
define('__UPLOAD_PATH_LOGS', MessageUtils::getConfig('base_data_path').'/logs');
define('__UPLOAD_PATH_FILES', MessageUtils::getConfig('base_data_path').'/files');

define('__DISPLAY_PATH', $url.MessageUtils::getConfig('base_data_display'));
define('__DISPLAY_PATH_IMG', $url.MessageUtils::getConfig('base_data_display').'/img');
define('__DISPLAY_PATH_FILES', $url.MessageUtils::getConfig('base_data_display').'/files');