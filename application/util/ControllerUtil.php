<?php namespace application\util;

use application\core\Route;
use application\service\AppPermissionService;
use application\service\AppUserService;
use application\service\AuthenService;
use application\util\AppUtil as AppUtils;
use application\util\FilterUtils as FilterUtil;
use application\util\MessageUtils as MessageUtil;
use Exception;

class ControllerUtil
{

    public static $MODULE_PARAM_2_ATT = 'module_param2';
    public static $MODULE_PARAM_3_ATT = 'module_param3';

    public static $DEVELOP_ROLE_ID = 1;


    public static $RPOFILE_CONTROLLER_NAME = 'Profile';
    public static $PROFILE_SHOP_CONTROLLER_NAME = 'ProfileShop';
    public static $PROFILE__TOPICS_CONTROLLER_NAME = 'ProfileTopics';
    public static $PROFILE__BLOGS_CONTROLLER_NAME = 'ProfileBlogs';
    public static $PROFILE_PHOTOS_CONTROLLER_NAME = 'ProfilePhotos';
    public static $PROFILE_FOLLOWING_CONTROLLER_NAME = 'ProfileFollowing';
    public static $PROFILE_FOLLOWERS_CONTROLLER_NAME = 'ProfileFollowers';

    public static $PROFILE_SHOP_PARAM_URL = 'shop';
    public static $PROFILE_TOPICS_PARAM_URL = 'topics';
    public static $PROFILE_BLOGS_PARAM_URL = 'blogs';
    public static $PROFILE_PHOTOS_PARAM_URL = 'photos';
    public static $PROFILE_FOLLOWING_PARAM_URL = 'following';
    public static $PROFILE_FOLLOWERS_PARAM_URL = 'followers';

    public static $THEME_GURUABLEDASHBOARD = 'guruabledashboard';


    public static function getUserIdSession()
    {
//		return FilterUtil::filterSessionInt('user_id');
        return FilterUtil::filterCookieVarInt('user_id') ? FilterUtil::filterCookieVarInt('user_id') : FilterUtil::filterSessionInt('user_id');
    }

    public static function getUserNameSession()
    {
//		return FilterUtil::filterSessionString('username');
        return FilterUtil::filterCookieVarString('username') ? FilterUtil::filterCookieVarString('username') : FilterUtil::filterSessionString('username');
    }

    public static function genUniqeToken()
    {
        $tokenNo = DateUtils::getTimeNow() . AppUtils::generateRandID();
        $_SESSION['uniqe_token_string'] = hash('sha512', $tokenNo . self::getUserAgent());
    }

    public static function getUniqeToken()
    {
        return FilterUtil::filterSessionString('uniqe_token_string');
    }

    public static function getUniqeTokenCookie()
    {
        return FilterUtil::filterCookieVarString(SystemConstant::COOKIE_UNIQE_TOKEN_ATT);
    }

    public static function getUserAgent()
    {
        return FilterUtil::filterServer('HTTP_USER_AGENT');
    }

    public static function isSecretLoged()
    {
//		$secretLoged =  FilterUtil::filterSessionInt('is_secret_loged');
        $secretLoged = FilterUtil::filterCookieInt('is_secret_loged');
//		echoln('secretLoged===>'.$secretLoged);
        if ($secretLoged == 1) {
            return true;
        }
        return false;
    }

    public static function getCurrentLocale()
    {
        /*
        $cookieLocaleValue = FilterUtil::filterCookieVarString(SystemConstant::$COOKIE_LOCALE_ATT);
        if(!AppUtil::isEmpty($cookieLocaleValue)){
//            echoln('$cookieLocaleValue Cookie==>'.$cookieLocaleValue);
            return $cookieLocaleValue;
        }else{
            //get default locale from configulation
            $defaultLocale = MessageUtils::getConfig('locale');
            if(!AppUtil::isEmpty($defaultLocale)){

//                echoln('Set Locale Cookie By Config==>'.$defaultLocale);
                //set to cookie
                ControllerUtil::setCookieByName(SystemConstant::$COOKIE_LOCALE_ATT,$defaultLocale);

                return $defaultLocale;

            }else{
                //set locale by brwser languge
                $defaultBrowserLang = AppUtil::getBrowserLang();
                if(AppUtil::isEmpty($defaultBrowserLang)){
                    $defaultBrowserLang = SystemConstant::$LOCALE_TH;
                }
//                echoln('getBrowserLang By Browser==>'.$defaultBrowserLang);
                //set to cookie
                ControllerUtil::setCookieByName(SystemConstant::$COOKIE_LOCALE_ATT,$defaultBrowserLang);
                return $defaultBrowserLang;
            }
        }
        */
        //set locale by brwser languge
        $defaultBrowserLang = AppUtil::getBrowserLang();
        if (AppUtil::isEmpty($defaultBrowserLang)) {
            $defaultBrowserLang = MessageUtils::getConfig('locale');
        }
        return $defaultBrowserLang;
    }

    public static function getLangugeDirList()
    {
        $dir = __SITE_PATH . '/resources/lang';
        return array_diff(scandir($dir), array('..', '.'));
    }

    public static function getLocaleFlag($locale)
    {
        switch ($locale) {
            case 'th':
                return 'flag-icon-th';
            case 'en':
                return 'flag-icon-gb';
            default :
                return 'flag-icon-th';
        }

    }

    public static function destroyCookieByName($name)
    {
        setcookie($name, "", time() - SystemConstant::COOKIE_TIME_1YEAR, "/");
    }

    public static function setCookieByName($name, $value)
    {
        setcookie($name, $value, time() + SystemConstant::COOKIE_TIME_1YEAR, "/");
    }


    /**
     * @param $stringTableName
     * @return string
     */
    public static function encodeParamId($stringTableName)
    {//app_user_login => appuserlogin

        $stringTableNameExplode = explode("_", $stringTableName);
        //$stringTableNameExplode = str_replace("_","",$stringTableName);
        $stringModuleHeadder = "";
        $isFirst = false;

        for ($i = 0; $i < count($stringTableNameExplode); $i++) {

//			if(!$isFirst){
            $stringModuleHeadder .= $stringTableNameExplode[$i];
//			}else{
//				$stringModuleHeadder .=ucfirst($stringTableNameExplode[$i]);
//			}
            $isFirst = true;
        }
        return $stringModuleHeadder;
    }

    /**
     * @param int $length
     * @return mixed
     */
    public static function getRadomSault($length = 16)
    {
        return hash('sha512', uniqid(openssl_random_pseudo_bytes($length), true));
    }

    public static function genHashPassword($passString, $random_salt)
    {
//        return hash('sha512',  $passString . $random_salt);
        return hash('sha512', get_env('APP_KEY') . $passString . $random_salt);
    }

    public static function hashSha512($string)
    {
        return hash('sha512', $string);
    }

    public static function uriSortConcat($sortField, $sortMode)
    {

        $curentSortMode = FilterUtil::filterGetString('sortMode');
        $curentSortField = FilterUtil::filterGetString('sortField');

        $nextSortMode = null;
        $nextSortField = null;
        if ($curentSortMode) {
            if ($curentSortMode == 'ASC') {
                $nextSortMode = 'DESC';
            } else {
                $nextSortMode = 'ASC';
            }
        } else {
            $nextSortMode = $sortMode;
        }


        $selfUri = null;
        $selfUriParam = null;
        $pagingParam = MessageUtil::getConfig('base_paging_param');
        $self = FilterUtil::filterServerUrl('REQUEST_URI');
//		if (FilterUtil::validateGetInt($pagingParam)) {
//
//			$current_page = FilterUtil::filterGetInt($pagingParam);
//
//			$selfSplit = explode("?", $self);
//			if(count($selfSplit)>1){
//				$selfUri = $selfSplit[0];
//				$selfUriParam = $selfSplit[1];
//				$selfUriParam = str_replace('page='.$current_page, '', $selfUriParam);
//			}
//			$selfPageLing =$selfUri.'?'.$selfUriParam.$pagingParam.'='.$current_page;
//			$selfPageLing = str_replace($curentSortMode, $nextSortMode, $selfPageLing);
//			if($curentSortField){
//				$selfPageLing = str_replace($curentSortField, $sortField, $selfPageLing);
//			}
//
//		}else{
        $selfSplit = explode("?", $self);
        if (count($selfSplit) > 1) {
            if ($curentSortMode) {
                $selfPageLing = str_replace($curentSortMode, $nextSortMode, $self);
                $selfPageLing = str_replace($curentSortField, $sortField, $selfPageLing);
            } else {
                $selfPageLing = $self . '&sortField=' . $sortField . '&sortMode=' . $nextSortMode;
            }

        } else {
            if ($curentSortMode) {
                $selfPageLing = str_replace($curentSortMode, $nextSortMode, $self);
                $selfPageLing = str_replace($curentSortField, $sortField, $selfPageLing);
            } else {
                $selfPageLing = $self . '?sortField=' . $sortField . '&sortMode=' . $nextSortMode;
            }
        }

//		}
        return $selfPageLing;
    }

    /**
     * Gen url id parameter to appUser=1 eg. http://localhost/bekaku/appUserView?appUser=1
     * @param $Object
     * @return bool|string
     */
    public static function genParamId($Object)
    {
        if (!$Object) {
            return false;
        }
        $param = self::encodeParamId($Object->getTableName());
        $param .= '=' . $Object->getId();
        return $param;
    }

    public static function f404Static($theme = "default")
    {
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT => false,
            SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('error.err_404')
        ], 404);
    }

    public static function f401Static($msg, $responseArray = null)
    {
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT => false,
            SystemConstant::SERVER_MSG_ATT => $msg
        ], 401);
    }

    public static function displaySqlError($err_msg_str, $theme = "default")
    {
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT => false,
            SystemConstant::SERVER_MSG_ATT => $err_msg_str
        ], 200);
    }

    public static function displayError($err_msg_str, $theme = "default")
    {
        jsonResponse([
            SystemConstant::SERVER_STATUS_ATT => false,
            SystemConstant::SERVER_MSG_ATT => $err_msg_str
        ], 200);
    }

    public static function setErrorMessage($err_msg_str)
    {
        echo $err_msg_str;
    }

    //success msg
    public static function setSuccessMessage($msg)
    {

        if (isset($_SESSION['SESSION_SUC_MSG'])) {
            $_SESSION['SESSION_SUC_MSG'] .= "<br>" . $msg;
        } else {
            $_SESSION['SESSION_SUC_MSG'] = $msg;
        }

    }

    public static function getSuccessMessage()
    {

        if (isset($_SESSION['SESSION_SUC_MSG'])) {
            return $_SESSION['SESSION_SUC_MSG'];
        } else {
            return false;
        }

    }

    public static function isAjax()
    {
//		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $serverreq = FilterUtil::filterServer('HTTP_X_REQUESTED_WITH');
        return isset($serverreq) && strtolower($serverreq) == 'xmlhttprequest';
    }

    public static function getDayOfWeekList()
    {
        return array(
            'MON',
            'TUE',
            'WED',
            'THU',
            'FRI',
            'SAT',
            'SUN',
        );
    }

    public static function randomColorCode($numbers)
    {
        switch ($numbers) {
            case 1:
                $returnCss = '#f44336';
                break;
            case 2:
                $returnCss = '#2196F3';
                break;
            case 3:
                $returnCss = '#009688';
                break;
            case 4:
                $returnCss = '#FFEB3B';
                break;
            case 5:
                $returnCss = '#795548';
                break;
            case 6:
                $returnCss = '#607D8B';
                break;
            case 7:
                $returnCss = '#9C27B0';
                break;
            case 8:
                $returnCss = '#4CAF50';
                break;
            case 9:
                $returnCss = '#FF5722';
                break;
            case 10:
                $returnCss = '#3F51B5';
                break;
            case 11:
                $returnCss = '#FFC107';
                break;
            case 12:
                $returnCss = '#9E9E9E';
                break;
            case 13:
                $returnCss = '#05112d';
                break;
            case 14:
                $returnCss = '#00BCD4';
                break;
            case 15:
                $returnCss = '#E91E63';
                break;
            case 16:
                $returnCss = '#a2a433';
                break;
            case 17:
                $returnCss = '#1d5e3f';
                break;
            case 18:
                $returnCss = '#3ef719';
                break;
            case 19:
                $returnCss = '#2938d3';
                break;
            default:
                $returnCss = '#000';
                break;
        }

        return $returnCss;
    }

    public static function randomColorForBarChart($numbers)
    {
        switch ($numbers) {
            case 1:
                $returnCss = '#0fbcf9';
                break;
            case 2:
                $returnCss = '#ffd32a';
                break;
            case 3:
                $returnCss = '#0be881';
                break;
            case 4:
                $returnCss = '#E91E63';
                break;
            case 5:
                $returnCss = '#a55eea';
                break;
            case 6:
                $returnCss = '#f5f6fa';
                break;
            case 7:
                $returnCss = '#FF5722';
                break;
            case 8:
                $returnCss = '#3666b9';
                break;
            case 9:
                $returnCss = '#00f140';
                break;
            case 10:
                $returnCss = '#7ad127';
                break;


            case 11:
                $returnCss = '#db7d98';
                break;
            case 12:
                $returnCss = '#ffa400';
                break;
            case 13:
                $returnCss = '#a34b2a';
                break;
            case 14:
                $returnCss = '#ef2db4';
                break;
            case 15:
                $returnCss = '#96bd5e';
                break;
            case 16:
                $returnCss = '#d4d5d0';
                break;
            case 17:
                $returnCss = '#8e61a2';
                break;
            case 18:
                $returnCss = '#c5a3a8';
                break;
            case 19:
                $returnCss = '#848079';
                break;
            case 20:
                $returnCss = '#3eb362';
                break;
            case 21:
                $returnCss = '#cd5a8c';
                break;
            case 22:
                $returnCss = '#d97234';
                break;
            case 23:
                $returnCss = '#8b2111';
                break;
            default:
                $returnCss = '#000';
                break;
        }

        return $returnCss;
    }

    public static function randomColorForPiChart($numbers)
    {
        switch ($numbers) {
            case 1:
                $returnCss = '#7bbf82';
                break;
            case 2:
                $returnCss = '#f5d767';
                break;
            case 3:
                $returnCss = '#df6e4c';
                break;
            case 4:
                $returnCss = '#3d7ab0';
                break;
            case 5:
                $returnCss = '#a55eea';
                break;
            case 6:
                $returnCss = '#0be881';
                break;
            case 7:
                $returnCss = '#f5f6fa';
                break;
            default:
                $returnCss = '#000';
                break;
        }

        return $returnCss;
    }

    public static function i18nextInit($customLocale = null)
    {
        $siteCurrentLocale = $customLocale ? $customLocale : self::getCurrentLocale();
        try {
            i18next::init($siteCurrentLocale, __SITE_PATH . '/resources/lang/' . $siteCurrentLocale . '/translation.json');
        } catch (Exception $e) {
            self::displayError('Caught exception: ' . $e->getMessage());
        }
        /*
         * i18next::getTranslation('authen.test');
         * i18next::getTranslation('authen.test_array_msg',array('mail' => $email)));
         *
         */

        /*
            echo 'category.navigation -> ' . i18nextT('category.navigation')."<br>";
            echo 'common.dog -> ' . iText('common.dog');
            echo '<br>';
            echo 'common.cat { count: 1 } -> ' . i18nextT('common.cat', array('count' => 1, 'count2' => 'ชนวีร์'));
            echo '<br>';
            echo 'common.cat { count: 2 } -> ' . i18nextT('common.cat', array('count' => 2));
            echo '<br>';
            echo 'app_name -> ' . i18nextT('app_name', array('count' => 'WIM'));
            echo '<br>';
            // get translation by key
            echo i18next::getTranslation('common.cat', array('count' => 1, 'count2' => 'ชนวีร์'));
            */
    }

    public static function callRestApi($url, $params = array(), $methodType = SystemConstant::METHOD_GET, $header = null)
    {
//        $params = array(
//            '_classDate' => '01/11/2561',
//            'xxx' => '01/11/2561',
//        );

        $options = array(
            'http' => array(
                'header' => $header ? $header : SystemConstant::CONTENT_TYPE_APPLICATION_JSON,
                'method' => $methodType,
                'content' => http_build_query($params),
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

    public static function getHttpStatusMessage($status)
    {
        $msg = $status ? i18next::getTranslation('httpStatus.' . $status) : null;
        return $msg;
    }

    public static function callApi($url, $params = array(), $methodType = SystemConstant::METHOD_GET, $header = null)
    {

        $response = null;
//        $data = array(// data u want to post
//            "username" => "test"
//        );

        $defaultHeader = array(
            SystemConstant::CONTENT_TYPE_APPLICATION_JSON,
            SystemConstant::ACCECP_CONTENT_TYPE_APPLICATION_JSON
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methodType);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header ? $header : $defaultHeader);

//        if(curl_exec($ch) === false) {
//            $returnCode= curl_error($ch);
//            $response[SystemConstant::SERVER_STATUS_CODE_ATT]=$returnCode;
//            $response[SystemConstant::SERVER_MSG_ATT]=self::getHttpStatusMessage($returnCode);
//            $response[SystemConstant::SERVER_RESPONSE_DATA]=null;
//            return $response;
//        }
        $errors = curl_error($ch);
        $result = curl_exec($ch);

        $data = json_decode($result, true);
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response[SystemConstant::SERVER_STATUS_CODE_ATT] = $returnCode;
        $response[SystemConstant::SERVER_MSG_ATT] = self::getHttpStatusMessage($returnCode);;
        $response['errorMessage'] = $errors;
        $response[SystemConstant::SERVER_RESPONSE_DATA] = $data;
        return $response;
    }

    public static function callFileApi($url, $params = array(), $methodType = SystemConstant::METHOD_GET, $header = null)
    {
        $response = null;
//        $data = array(// data u want to post
//            "username" => "test"
//        );

        $defaultHeader = array(
            SystemConstant::CONTENT_TYPE_APPLICATION_JSON,
            SystemConstant::ACCECP_CONTENT_TYPE_APPLICATION_JSON
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methodType);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header ? $header : $defaultHeader);
        if (curl_exec($ch) === false) {
            $returnCode = curl_error($ch);
            $response[SystemConstant::SERVER_STATUS_CODE_ATT] = $returnCode;
            $response[SystemConstant::SERVER_MSG_ATT] = self::getHttpStatusMessage($returnCode);
            $response[SystemConstant::SERVER_RESPONSE_DATA] = null;
            ControllerUtil::displayError($response);
        }
        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

}