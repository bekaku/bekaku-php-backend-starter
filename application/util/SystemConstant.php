<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 3/8/2017
 * Time: 2:20 PM
 */

namespace application\util;


class SystemConstant
{
    const CODE_VERSION = 1;
    const LOCALE_ATT = "appLocale";
    const HEADER_LOCALE_ATT = "X-Locale";
    const LOCALE_TH = "th";
    const LOCALE_EN = "en";
    const PRODUCTION_MODE_DEVELOP = "development";
    const PRODUCTION_MODE_TESTING = "testing";
    const PRODUCTION_MODE_PRODUCTION = "production";

    const HTTP_STR = "http://";
    const HTTPS_STR = "https://";
    const LOCALHOST_URL = "localhost";

    //theme
    const THEME_DEFAULT = 'default';
    public static $THEME_LIST = array("default");

    //cookie
//    public static $COOKIE_TIME_30DAYS = 86400 * 30;//30 days , 86400 = 1 day
//    public static $COOKIE_TIME_1YEAR = 86400 * 30 * 12;//30 * 12 = 1 year
    const COOKIE_TIME_30DAYS = 2592000;//86400 * 30    =30 days , 86400 = 1 day
    const COOKIE_TIME_1YEAR = 31104000;//86400 * 30 * 12 =30 * 12 = 1 year
    const COOKIE_LOCALE_ATT = "COOKIE_LOCALE_ATT";
    const COOKIE_UNIQE_TOKEN_ATT = "uniqe_token_ck_string";
    const SEEION_ID_NAME_ATT = "sec_session_id";

    //att
    const APP_PAGINATION_ATT = "appPagination";
    const APP_VALIDATE_ERR_ATT = "validateErrors";
    const APP_IMAGE_FILE_UPLOAD_ATT = "imgUpload";
    const APP_IMAGE_FILE_DELETE_ATT = "imgDel";


    /*
    |--------------------------------------------------------------------------
    | FOR APP
    |--------------------------------------------------------------------------
    */
    const LOGIN_SESSION_EXPIRE = "sessionExpire";
    const SERVER_STATUS_ATT = "status";
    const SERVER_STATUS_CODE_ATT = "statusCode";
    const SERVER_MSG_ATT = "message";
    const SERVER_RESPONSE_DATA = "data";
    const USER_API_KEY_ATT = "apiKey";
    const JWT_USER_ID_ATT = "uid";

    const PAGE_ATT = 'page';
    const PER_PAGE_ATT = 'perPage';
    const DATA_LIST_ATT = 'dataList';
    const ENTITY_ATT = 'entity';
    const ID_PARAM = '_id';
    const ID_PARAMS = '_ids';
    const UNDER_SCORE = '_';
    const SORT_FIELD_ATT = 'sortField';
    const SORT_MODE_ATT = 'sortMode';

    //header
    const CONTENT_TYPE_TEXT_HTML = "Content-Type: text/html; charset=utf-8";
    const CONTENT_TYPE_APPLICATION_JSON = "Content-Type: application/json; charset=utf-8";
    const CONTENT_TYPE_TEXT_PLAIN = "Content-Type: text/plain";
    const CONTENT_TYPE_IMAGE_JPEG = "Content-Type: image/jpeg";
    const CONTENT_TYPE_APPLICATION_ZIP = "Content-Type: application/zip";
    const CONTENT_TYPE_APPLICATION_PDF = "Content-Type: application/pdf";
    const CONTENT_TYPE_APPLICATION_URLENCODED = "Content-Type: application/x-www-form-urlencoded; charset=utf-8";


    const CONTENT_TYPE_AUDIO_MPEG = "Content-Type: audio/mpeg";

    const ACCECP_CONTENT_TYPE_APPLICATION_JSON = "Accept: application/json";
    const ACCECP_CONTENT_TYPE_APPLICATION_PDF = "Accept: application/pdf";
    //
    const METHOD_POST = "POST";
    const METHOD_GET = "GET";
    const METHOD_PUT = "PUT";
    const HTTP_OK = 200;

    //Api Client
    const API_NAME_ATT = "apiClient";
    //OS
    const OS_UNKNOWN = 1;
    const OS_WIN = 2;
    const OS_LINUX = 3;
    const OS_OSX = 4;
}