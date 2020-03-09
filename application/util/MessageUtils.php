<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 29/12/2015
 * Time: 7:09 PM
 */

namespace application\util;


class MessageUtils
{
    /*
     | way to use eg. MessageUtil::getConfig('mysql.driver')
     | way to use eg. MessageUtil::getConfig('language')
     */
    public static function getConfig($params = null)
    {
        $confString = null;
        if($params){

            // if(file_exists(__SITE_PATH.'/application/configuration/app.php')) {
            //     $configArray = require __SITE_PATH . "/application/configuration/app.php";
            //     $confString = self::getValueFromArray($configArray, $params);
            // }
             $configPath=null;
            if(AppUtil::getCurrentOS() == SystemConstant::OS_WIN){
                $configPath = 'D:/php_htdocs/githup/php/bekaku-php-backend-starter/data/configuration/app.php';
            }else if(AppUtil::getCurrentOS() == SystemConstant::OS_LINUX){
                $configPath = '/var/bekaku-php-backend-starter/data/configuration/app.php';
            }else if(AppUtil::getCurrentOS() == SystemConstant::OS_OSX){
                $configPath = '/Users/bekaku/bekaku-php-backend-starter/data/configuration/app.php';
            }else{
                ControllerUtil::displayError('Config file not found.');
            }
            
             if(file_exists($configPath)) {
                $configArray = require $configPath;
                $confString = self::getValueFromArray($configArray, $params);
            }
        }
        return $confString;
    }
    public static function getVendorConfig($params = null, $isInternalFile=true)
    {
        $cdnOnline = self::getConfig('cdn_online');
//        echoln('cdn-Mode====>'.$cdnOnline);
        $confString = null;
        if($params){

            if(file_exists(__SITE_PATH.'/application/configuration/vendorConf.php')) {
                $configArray = require __SITE_PATH . "/application/configuration/vendorConf.php";
                $confString = self::getValueFromArray($configArray, $params);
            }
        }
        if($isInternalFile || !$cdnOnline){
            return __RESOURCES.$confString;
        }else{
            return $confString;
        }

    }
    public static function genScriptCustom($list=array())
    {
        if(!AppUtil::isArrayEmpty($list)){

            foreach ($list AS $js){
                echo "<script type=\"text/javascript\" src=\"".$js."\"></script>";
            }
        }
    }
    public static function genStyleCustom($list=array())
    {
        if(!AppUtil::isArrayEmpty($list)){

            foreach ($list AS $css){
                echo "    <link rel=\"stylesheet\" type=\"text/css\" href=\"" . $css . "\"> \r";
            }
        }
    }

    public static function getMessage($params){
        $locale = MessageUtils::getConfig('locale');
        $msgString = null;
        if($params){
            $messageList = array();
            if(file_exists(__SITE_PATH . '/resources/lang/' . $locale . '/app.php')) {
                $msgAppArray = require __SITE_PATH . '/resources/lang/' . $locale . '/app.php';
                array_push($messageList, $msgAppArray);
            }
            if(file_exists(__SITE_PATH . '/resources/lang/' . $locale . '/error.php')) {
                $msgErrorArray = require __SITE_PATH . '/resources/lang/' . $locale . '/error.php';
                array_push($messageList, $msgErrorArray);
            }
            if(file_exists(__SITE_PATH . '/resources/lang/' . $locale . '/model.php')) {
                $msgModelArray = require __SITE_PATH . '/resources/lang/' . $locale . '/model.php';
                array_push($messageList, $msgModelArray);
            }
            if(file_exists(__SITE_PATH . '/resources/lang/' . $locale . '/date.php')) {
                $msgDateArray = require __SITE_PATH . '/resources/lang/' . $locale . '/date.php';
                array_push($messageList, $msgDateArray);
            }
            $msgString = self::getValueFromMessageArray($messageList, $params);
        }
        return $msgString;
    }
    private static function getValueFromMessageArray(&$msgArr, $param){
        foreach($msgArr as $msg){
            if(isset($msg[$param])){
                return $msg[$param];
                break;
            }
        }
        return null;
    }
    private static function getValueFromArray(&$config, $params = null)
    {
        $confString = null;
        if($params){

            $keys = explode(".",$params);
            if(count($keys)>=2){
                while (count($keys) > 1){
                    $key = array_shift($keys);
                    if ( ! isset($config[$key]) or ! is_array($config[$key]))
                    {
                        $config[$key] = array();
                    }
                    $config =& $config[$key];
                }
                $confString = $config[array_shift($keys)];
            }else{
                $confString = $config[$keys[0]];
            }
        }
        return $confString;
    }
}