<?php namespace application\util;

use application\util\DateUtils as DateUtil;
use application\util\MessageUtils as MessageUtils;
use AutoEmbed;
use LogicException;
use SplFileObject;

class AppUtil
{

    public static function requireOnceAllPhp($path)
    {
        //foreach (glob(__SITE_PATH.'/application/serviceImpl/*.php') as $filename){
        //    require_once $filename;
        //}
        foreach (glob("{$path}/*.php") as $filename) {
            if (!empty($filename)) {
                require_once $filename;
            }
        }
        return true;
    }

    public static function genModuleNameFormat($stringTableName)
    {//app_user_login -> appUserLogin

        $stringTableNameExplode = explode("_", $stringTableName);
        $stringModuleHeadder = "";
        $isFirst = FALSE;

        for ($i = 0; $i < count($stringTableNameExplode); $i++) {

            if (!$isFirst) {
                $stringModuleHeadder .= $stringTableNameExplode[$i];
            } else {
                $stringModuleHeadder .= ucfirst($stringTableNameExplode[$i]);
            }
            $isFirst = true;
        }
        return $stringModuleHeadder;

    }

    public static function genComponentNameFormat($stringTableName)
    {//app_user_login -> app-user-login

        return str_replace("_", "-", $stringTableName);

    }

    public static function genPublicMethodName($stringTableName)
    {//app_user_login -> AppUserLogin

        $stringTableNameExplode = explode("_", $stringTableName);
        $stringModuleHeadder = "";

        for ($i = 0; $i < count($stringTableNameExplode); $i++) {
            $stringModuleHeadder .= ucfirst($stringTableNameExplode[$i]);
        }
        return $stringModuleHeadder;

    }

    public static function getUpperString($string)
    {//app_user_login -> APP_USER_LOGIN
        return strtoupper($string);
    }

    public static function getLowerString($string)
    {//APP_USER_LOGIN -> app_user_login
        return strtolower($string);
    }

    public static function getUpperFirstString($string)
    {//app_user_login -> App_user_login
        return ucfirst($string);
    }

    public static function getUrlFromTableName($string)
    {//app_user_login -> appuserlogin
        return str_replace('_', '', $string);
    }

    //how to use AppUtil::checkLimitfile($file,$limit_size_kb) ;

    public static function checkLimitfile($file, $limit_size_kb)
    {
        if (isset($file)) {

            //$limit_size=524288;//512kb to byte to limit size
            $limit_size_byte = $limit_size_kb * 1024;//convert fb to byte
            $file_size = $file['size'];
            if ($file_size > $limit_size_byte) {
                return true;
            } else {
                return false;
            }

        }
        return false;

    }

    public static function getSizeFromPath($path)
    {

        $size = 0;
        if (file_exists($path)) {
            $size = filesize($path); // outputs in 1024 bytes
            //$size = $size/1024; // convert byte to kb
            $size = $size / 1024; // convert kb to mb

        }
        return number_format($size);
    }

    public static function checkYearMonthFolder($pathUpload, $yearMonthFolder)
    {//   2015-07-06 19:51:37

        if ($pathUpload && $yearMonthFolder) {

            $yearMonthFolder = DateUtil::getYearAndMonthFromDate($yearMonthFolder);

            if (is_dir($pathUpload . "/" . $yearMonthFolder)) {
                return true;
            } else {
                self::createFolder($pathUpload . "/" . $yearMonthFolder, "0777");
                return false;
            }

        }
        return false;

    }

    public static function getFileExtension($fileName)
    {

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($ext) {
            return $ext;
        }
        return false;
    }

    public static function doDelfileFromPath($path)
    {
        if (file_exists($path)) {
            @unlink($path);
            return true;
        }
        return true;
    }

    public static function doRenameFile($path, $old_file_name, $new_file_name)
    {
        rename($path . "/" . $old_file_name, $path . "/" . $new_file_name);
    }

    public static function isImageFile($path)
    {
        $a = @getimagesize($path);
        $image_type = $a[2];
        if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
            return true;
        }
        return false;
    }

    public static function isEmpty($var)
    {
        $var = trim($var);
        if ($var !== '' && strlen($var) > 0 && isset($var) && $var != null) {
            return false;
        }
        return true;
    }

    public static function isArrayEmpty($arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                if (!empty($value) || $value != NULL || $value != "") {
                    return false;
                    break;//stop the process we have seen that at least 1 of the array has value so its not empty
                }
            }
            return true;
        }
    }

    public static function isObjectOrArray($obj)
    {
        if (is_array($obj) || is_object($obj)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getMoneyFormat($number)
    {
        // money_format not support in windows
//		setlocale(LC_MONETARY,"th_TH");
//		return money_format('%i',$number);

//		return '$' . number_format($number, 2);
        return number_format($number, 2);
    }

    public static function convertStringToInt($var)
    {
        return (int)$var;
    }

    public static function convertStringToFloat($var)
    {
        return (float)$var;
    }

    public static function convertStringToDouble($var)
    {
        return (double)$var;
    }

    public static function getCurrentUrl()
    {
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }

    public static function createFolder($dirPath, $permission)
    {

        //permission
        //0600 Read and write for owner, nothing for everybody else
        //0644 Read and write for owner, read for everybody else
        //0755 Everything for owner, read and execute for others
        //0750 Everything for owner, read and execute for owner's group
        //0777 Everything for public

        //path eg. view/users is mean we want to create users folder
        if (!file_exists($dirPath)) {
            @mkdir($dirPath, $permission);
        }

        /*
        if (!file_exists($dirPath)) {
            if($permission==MessageUtils::getConfig('chmod_mode.file_read_mode')) {
                @mkdir($dirPath, 0644);
                @chmod($dirPath, 0644);

            }else if($permission==MessageUtils::getConfig('chmod_mode.file_write_mode')){
                @mkdir($dirPath, 0666);
                @chmod($dirPath, 0666);
            }else if($permission==MessageUtils::getConfig('chmod_mode.dir_read_mode')){
                @mkdir($dirPath, 0755);
                @chmod($dirPath, 0755);
            }else if($permission==MessageUtils::getConfig('chmod_mode.dir_write_mode')){
                @mkdir($dirPath, 0777);
                @chmod($dirPath, 0777);
            }
        }
        */
    }

    public static function deleteFolder($dirPath)
    {

        if (file_exists($dirPath)) {//already have this folder

            $dir = @opendir($dirPath);//read file in this folder
            while (($data = @readdir($dir)) !== false) {
                if ($data != "." && $data != "..") {
                    @Unlink($dirPath . "/" . $data); //delete file in this folder before delete folder
                }
            }
            closedir($dir);
            @rmdir($dirPath);//delete folder
        }
    }

    public static function parseFile($filename)
    {
        $file = null;
        if (file_exists($filename)) {
            try {
                $file = new SplFileObject($filename);
            } catch (LogicException $exception) {
                die('SplFileObject : ' . $exception->getMessage());
            }

//            $i=0;
//            while ($file->valid()) {
//                $i++;
//                $line = $file->fgets();
//                //do something with $line
//                echo('$i='.$i.' '.$line);
//            }

            //don't forget to free the file handle.
//            $file = null;
        }
        return $file;
    }

    public static function subStr($str, $lenth)
    {

        if ($str) {

            return iconv_substr($str, 0, $lenth, "UTF-8") . "...";

        }
        return false;
    }

    public static function convertCp874ToUtf8($str)
    {
        if ($str) {

            return iconv('UTF-8', 'cp874', $str);

        }
        return false;
    }

    public static function randomInt($min = 0, $max = 100)
    {
        return rand($min, $max);
    }

    public static function randomFlot($min = 0, $max = 100)
    {
        try {
            return random_int($min, $max - 1) + (random_int(0, PHP_INT_MAX - 1) / PHP_INT_MAX);
        } catch (\Exception $e) {
        }

        return 0;
    }

    /* generateRandID - Generates a string made up of randomized letters (lower and upper case)
    and digits and returns the md5 hash of it to be used as a userid */
    public static function generateRandID($length = 16)
    {
        return md5(DateUtil::getTimeNow() . self::generateRandStr($length));
    }

    public static function generateRandStr($length = 16)
    {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

    public static function generateToken($length = 32)
    {
        // Create random token
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }

    //returns the real ip address of a user
    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getNormalIpAddr()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getSesstionId()
    {
        return session_id();
    }

    public static function getBrowser()
    {


        $iphone = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'iphone');
        $android = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'android');
        $palmpre = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'webos');
        $ipod = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'ipod');
        $ipad = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'ipad');

        $firefox = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'firefox');
        $ie = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'msie');
        $safari = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'safari');
        $webkit = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'webkit');
        $oprea = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'opera');
        $chrome = strpos(strtolower(" " . $_SERVER['HTTP_USER_AGENT']), 'chrome');

        if ($firefox) {
            return 'FFX';
        }
        if ($ie) {

            $version = substr($_SERVER['HTTP_USER_AGENT'], $ie + 4, 1);
            $versionArray = array('6', '7', '8', '9');

            if (in_array($version, $versionArray)) {
                return 'IE' . $version;
            } else {
                return 'IEO';
            }
        }

        if ($webkit) {
            if ($safari && !$chrome) {
                return 'SAF';
            } else if ($chrome) {
                return 'GOO';
            } else {
                return 'WBK';
            }
        }

        if ($oprea) {
            return 'OPA';
        }

        //these are OS not Browsers?

        if ($iphone) {
            return 'IPHO';
        }
        if ($android) {
            return 'ANDR';
        }
        if ($palmpre) {
            return 'PALM';
        }
        if ($ipod) {
            return 'IPOD';
        }
        if ($ipad) {
            return 'IPAD';
        }

        return 'OBW';

    }

    /**
     *
     * Enter description here ...
     */
    public static function getOS()
    {
        $OSList = array

        (

            // Match user agent string with operating systems

            'WIN3' => 'Win16',

            'WIN95' => '(Windows 95)|(Win95)|(Windows_95)',

            'WIN98' => '(Windows 98)|(Win98)',

            'WIN2K' => '(Windows NT 5.0)|(Windows 2000)',

            'WINXP' => '(Windows NT 5.1)|(Windows XP)',

            'WINS23' => '(Windows NT 5.2)',

            'WINV' => '(Windows NT 6.0)',

            'WIN7' => '(Windows NT 6.1)',

            'WINNT' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',

            'WINME' => 'Windows ME',

            'OBSD' => 'OpenBSD',

            'SUNOS' => 'SunOS',

            'LIN' => '(Linux)|(X11)',

            'MACOS' => '(Mac_PowerPC)|(Macintosh)',

            'QNX' => 'QNX',

            'BEOS' => 'BeOS',

            'OS/2' => 'OS/2',

            'SSB' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'

        );

        // Loop through the array of user agents and matching operating systems
        foreach ($OSList as $CurrOS => $Match) {
            // Find a match
            if (preg_match("/" . $Match . "/i", $_SERVER['HTTP_USER_AGENT'])) {

                // We found the correct match
                return $CurrOS;
                break;

            }

        }
        return 'OOS';

    }

    public static function _wordwrap($text, $valuelimit, $seperator)
    {
        $split = explode(" ", $text);
        foreach ($split as $key => $value) {
            if (strlen($value) > $valuelimit) {
                $split[$key] = chunk_split($value, $valuelimit, $seperator);
            }
        }
        return implode(" ", $split);
    }

    public static function isThaiAlphabet($alphabet)
    {

        $mystring = 'กขฃคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮ';
        $findme = $alphabet;//'a';
        $pos = strpos($mystring, $findme);

        // The !== operator can also be used.  Using != would not work as expected
        // because the position of 'a' is 0. The statement (0 != false) evaluates
        // to false.
        if ($pos !== false) {
            /*echo "The string '$findme' was found in the string '$mystring'";
                echo " and exists at position $pos";*/
            return TRUE;
        } else {
            return FALSE;
            /* echo "The string '$findme' was not found in the string '$mystring'";*/
        }
    }

    public static function findStringInString($stringList, $findMe)
    {

        $pos = strpos($stringList, $findMe);
        // The !== operator can also be used.  Using != would not work as expected
        // because the position of 'a' is 0. The statement (0 != false) evaluates
        // to false.
        if ($pos !== false) {
            /*echo "The string '$findme' was found in the string '$mystring'";
                echo " and exists at position $pos";*/
            return true;
        } else {
            return false;
            /* echo "The string '$findme' was not found in the string '$mystring'";*/
        }
    }

    public static function tolink($text)
    {//split url type from message
        return preg_replace(
            array(
                '/(?(?=<a[^>]*>.+<\/a>)
             (?:<a[^>]*>.+<\/a>)
             |
             ([^="\']?)((?:https?|ftp|bf2|):\/\/[^<> \n\r]+)
         )/iex',
                '/<a([^>]*)target="?[^"\']+"?/i',
                '/<a([^>]+)>/i',
                '/(^|\s)(www.[^<> \n\r]+)/iex',
                '/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)
       (\\.[A-Za-z0-9-]+)*)/iex'
            ),
            array(
                "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\">\\2</a>\\3':'\\0'))",
                '<a\\1',
                '<a\\1 target="_blank">',
                "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\">\\2</a>\\3':'\\0'))",
                "stripslashes((strlen('\\2')>0?'<a href=\"mailto:\\0\">\\0</a>':'\\0'))"
            ),
            $text
        );

    }


    public static function tinyUrl($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    /*
     * YOUTUBE PLUGIN
     */
    public static function getYoutubeThumbnail($youtube_url, $type = 0)
    {
        //0.jpg,2.jpg,hq1.jpg, hq2.jpg, hq3.jpg , hqdefault.jpg,default.jpg
        $yu_id = explode('www.youtube.com/watch?v=', $youtube_url);
        $thumb = "http://img.youtube.com/vi/" . $yu_id[1] . "/" . $type . ".jpg";
        return $thumb;
    }

    public static function uniqueMultidimArray($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    /*
     * $userdb=Array
        (
            (0) => Array
                (
                    (uid) => '100',
                    (name) => 'Sandra Shush',
                    (url) => 'urlof100'
                ),

            (1) => Array
                (
                    (uid) => '5465',
                    (name) => 'Stefanie Mcmohn',
                    (pic_square) => 'urlof100'
                ),

            (2) => Array
                (
                    (uid) => '40489',
                    (name) => 'Michael',
                    (pic_square) => 'urlof40489'
                )
        );

     * array_search(40489, array_column($userdb, 'uid'));
     */
    public static function findObjectInArray($arrayList, $keyValueSearch, $fieldName)
    {
        $item = null;
        $positionExits = array_search($keyValueSearch, array_column($arrayList, $fieldName));
//        echoln(count($arrayList).' keyValueSearch=>'.$keyValueSearch.', position =>'.$positionExits);
        if (is_numeric($positionExits)) {
            $item = $arrayList[$positionExits];
        }


//        if($positionExits!=-1){
//            echoln('$positionExits>=0');
//            $item = $arrayList[$positionExits];
//        }

        return $item;
    }

    public static function removeObjectFromArray($array, $keyAtt, $objValue)
    {

        foreach ($array as $k => $v) {
            foreach ($array[$k] as $key => $value) {
                if ($key === $keyAtt && $value === $objValue) { //If Value of 2D is equal to user and cat
                    unset($array[$k]); //Delete from Array
                }
            }
        }
        return array_values($array);
    }

    /*
     * http://php.net/manual/en/function.round.php
     * PHP_ROUND_HALF_UP
     * PHP_ROUND_HALF_DOWN
     * PHP_ROUND_HALF_EVEN
     * PHP_ROUND_HALF_ODD
     */
    public static function roundHalf($val, $place, $function)
    {
        return round($val, $place, $function);
    }

    public static function roundUp($value, $places = 0)
    {
        if ($places < 0) {
            $places = 0;
        }
        $mult = pow(10, $places);
        return ceil($value * $mult) / $mult;
    }

// round_out:
// rounds a float away from zero to a specified number of decimal places
    public static function roundOut($value, $places = 0)
    {
        if ($places < 0) {
            $places = 0;
        }
        $mult = pow(10, $places);
        return ($value >= 0 ? ceil($value * $mult) : floor($value * $mult)) / $mult;
    }

    public static function calculatePercentagetBy($value, $total)
    {
        if ($value == 0 || $total == 0) {
            return 0;
        }
        if ($value <= 0) {
            return 0;
        }
        return ($value * 100) / $total;
    }

    public static function getBrowserLang()
    {
//        return substr(Filter['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return substr(FilterUtils::filterServer('HTTP_ACCEPT_LANGUAGE'), 0, 2);
    }

    public static function getCurrentOS()
    {
        $os = strtoupper(substr(PHP_OS, 0, 3));
        switch ($os) {
            case 'WIN':
                return SystemConstant::OS_WIN;
                break;
            case 'DAR':
                return SystemConstant::OS_OSX;
                break;
            case 'LIN':
                return SystemConstant::OS_LINUX;
                break;
            default:
                return SystemConstant::OS_UNKNOWN;
                break;
        }
        // switch (true) {
        //     case stristr(PHP_OS, 'DAR'): return SystemConstant::OS_OSX;
        //     case stristr(PHP_OS, 'WIN'): return SystemConstant::OS_WIN;
        //     case stristr(PHP_OS, 'LINUX'): return SystemConstant::OS_LINUX;
        //     default : return SystemConstant::OS_UNKNOWN;
        // }
    }

    public static function getServerPort()
    {
        return MessageUtils::getConfig('secure') ?
            (MessageUtils::getConfig('ssl_port') != 443 ? ":" . MessageUtils::getConfig('ssl_port') : "") :
            (MessageUtils::getConfig('url_port') != 80 ? ":" . MessageUtils::getConfig('url_port') : "");
    }

    public static function getServerIp()
    {
        return (MessageUtils::getConfig('secure') ? SystemConstant::HTTPS_STR : SystemConstant::HTTP_STR)
            . MessageUtils::getConfig('url')
            . AppUtil::getServerPort();
    }

    public static function getDisplayDataPath()
    {
        return self::getServerIp() . MessageUtils::getConfig('base_data_display');
    }

    public static function getSiteMetaData($url, $atts = ['url', 'title', 'image', 'description'])
    {
        if (empty($url)) {
            return null;
        }
        $graph = OpenGraph::fetch($url);
        if (!$graph) {
            return null;
        }
        $metas = $graph->getDetail($graph);
        $item = null;
        foreach ($atts as $key) {
            $item[$key] = isset($metas[$key]) ? $metas[$key] : null;
        }

        return $item;
    }
}