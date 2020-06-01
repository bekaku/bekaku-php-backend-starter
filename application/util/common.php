<?php

use application\core\AppController as AppController;
use application\util\Log;
use application\util\MessageUtils as MessageUtil;
use application\util\SystemConstant;

function cors()
{
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}

function restApiHeader()
{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

if (!function_exists('sec_session_start')) {
    function sec_session_start()
    {
        $session_name = SystemConstant::SEEION_ID_NAME_ATT;   // Set a custom session name
        $secure = MessageUtil::getConfig('secure');

        // This stops JavaScript being able to access the session id.
        $httponly = true;

        // Forces sessions to only use cookies.
        if (ini_set('session.use_only_cookies', 1) === false) {
            AppController::$errorCustomMessage = "Could not initiate a safe session (ini_set) <br>";
            exit();
        }

        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

        // Sets the session name to the one set above.
        session_name($session_name);

        session_start();            // Start the PHP session
        session_regenerate_id();    // regenerated the session, delete the old one.
    }
}
if (!function_exists('destroySession')) {
    function destroySession()
    {
        // Unset all session values
        $_SESSION = array();

        // get session parameters
        $params = session_get_cookie_params();

        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

        // Destroy session
        session_destroy();
    }
}

if (!function_exists('is_really_writable')) {

    function is_really_writable($file)
    {
        // If we're on a Unix server with safe_mode off we call is_writable
        if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE) {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand(1, 100) . mt_rand(1, 100));
            if (($fp = @fopen($file, MessageUtil::getConfig('fopen_mode.fopen_write_create'))) === false) {
                return FALSE;
            }

            fclose($fp);
            @chmod($file, MessageUtil::getConfig('chmod_mode.dir_write_mode'));
            @unlink($file);
            return TRUE;
        } elseif (!is_file($file) OR ($fp = @fopen($file, MessageUtil::getConfig('fopen_mode.fopen_write_create'))) === false) {
            return FALSE;
        }

        fclose($fp);
        return TRUE;
    }
}
if (!function_exists('get_env')) {
    function get_env($key = null)
    {
        if(!$key){
            return null;
        }
        $parsed = parse_ini_file(__SITE_PATH . '/.env');
        if (isset($parsed[$key])) {
            return $parsed[$key];
        }
        return $parsed;
    }
}

/*
 *
 * Enter description here ...
 * How to use--------->> log_message("error", "loging ......by VEe", FALSE, "loginLog");
 */
if (!function_exists('log_message')) {
    function log_message($level = 'error', $message = null, $php_error = false, $fileName = "")
    {
        $canLog = MessageUtil::getConfig('log.can_log');
        if ($canLog) {
            if (MessageUtil::getConfig('log.log_threshold') == 0) {
                return;
            }
//		require __SITE_PATH.'/application/util/Log.php';
            $_log = new Log();
            $_log->write_log($level, $message, $php_error, $fileName);
        }
    }

}

if (!function_exists('v_goto'))//change goto function with v_goto
{
    function v_goto($url)
    {
        //echo '<meta http-equiv=refresh content=0;URL='.BASEURL."login".'>';
        //header( 'Location: '.BASEURL.'login' ) ;
        header('Location: ' . $url);
    }

}
if (!function_exists('v_rediect'))//change goto function with v_goto
{
    function v_rediect($url)
    {
        header('Location: ' . _BASEURL . $url);
    }

}

if (!function_exists('v_gotoRefresh'))//change goto function with v_goto
{
    function v_gotoRefresh($time, $url)
    {
        return '<meta http-equiv=refresh content=' . $time . ';URL=' . $url . '>';
    }

}

function echoln($val = null)
{

    echo "\n<br>" . $val . "<br>\n";

}

if (!function_exists('v_gotohttps'))//change goto function with v_goto
{
    function v_gotohttps()
    {
        if (@$_SERVER["HTTPS"] != "on") {
            header('Location: https://www.otep.go.th/admin');
            exit;
        }
    }

}

function curPageURL()
{
    $pageURL = 'http';
    if (@$_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;// http://localhost/outsource/junlachai/km.php?id=11
}

function curPageName()
{
    return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);//km.php from  http://localhost/outsource/junlachai/km.php?id=11
}

function curPageNameParam()
{
    $uri = $_SERVER["REQUEST_URI"];
    if (strpos($uri, '?') !== false) {
        $uriEx = explode("?", $uri);
        $uri = '?' . @$uriEx[1];
    } else {
        $uri = "";
    }
    $pageURL = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1) . $uri;
    return $pageURL;
}

function jsonResponse($res = array(), $status = 200)
{
    if (!headers_sent()) {
        header(SystemConstant::CONTENT_TYPE_APPLICATION_JSON);
        header("HTTP/1.0 $status ");
    }
    echo json_encode($res);
    exit();
}