<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 29/12/2015
 * Time: 2:52 PM
 */

namespace application\util;


use DateTime;

class FilterUtils
{

    /* Unfilter
    | This filter removes data that is potentially harmful for your application.
    | It is used to strip tags and remove or encode unwanted characters.
    |
    */
    public static function unSafeFilterGet($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_UNSAFE_RAW);
    }

    public static function unSafeFilterPost($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_UNSAFE_RAW);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Validate input => validate right format
    |--------------------------------------------------------------------------
    */
    /* validate email*/
    public static function validateGetEmail($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePostEmail($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_EMAIL);
    }

    //Validates an integer
    public static function validateGetInt($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_INT);
    }

    public static function validatePostInt($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_INT);
    }

    //Validates a boolean
    public static function validateGetBoolean($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_BOOLEAN);
    }

    public static function validatePostBoolean($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_BOOLEAN);
    }

    //Validates a float
    public static function validateGetFloat($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_FLOAT);
    }

    public static function validatePostFloat($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_FLOAT);
    }

    // 	Validates an IP address
    public static function validateGetIp($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_IP);
    }

    public static function validatePostIp($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_IP);
    }

    //Validates a URL
    public static function validateGetUrl($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_URL);
    }

    public static function validatePostUrl($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_URL);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter sanitize input => remove and replace unsafe charactor
    |--------------------------------------------------------------------------
    */
    /* Removes tags/special characters eg. html tags from a string */
    public static function filterGetString($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_STRING);
    }

    public static function filterPostString($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_STRING);
    }

    /* Removes all illegal characters from an e-mail address */
    public static function filterGetEmail($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_EMAIL);
    }

    public static function filterPostEmail($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_EMAIL);
    }

    /* Encode special characters in the $url variable: */
    public static function filterGetEncode($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_ENCODED);
    }

    public static function filterPostEncode($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_ENCODED);
    }

    /* Apply addslashes() to single quote ('),double quote ("),backslash (\),NULL */
    public static function filterGetAddSlahes($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_MAGIC_QUOTES);
    }

    public static function filterPostAddSlahes($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_MAGIC_QUOTES);
    }

    /* Removes all characters except digits and + - */
    public static function filterGetInt($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function filterPostInt($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_INT);
    }

    /* Remove all characters, except digits, +- and optionally .,eE */
    public static function filterGetFloat($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_FLOAT);
    }

    public static function filterPostFloat($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_FLOAT);
    }

    /* Removes special characters  "Is Peter <smart>" in view source see Is Peter &lt;smart&gt;*/
    public static function filterGetSpecialChar($var)
    {
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function filterPostSpecialChar($var)
    {
        return filter_input(INPUT_POST, $var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter sanitize Server
    |--------------------------------------------------------------------------
    */
    public static function filterServer($var)
    {
        return filter_input(INPUT_SERVER, $var, FILTER_UNSAFE_RAW);
//        return filter_var($_SERVER[$var], FILTER_UNSAFE_RAW);
    }

    public static function filterServerUrl($var)
    {
        return filter_input(INPUT_SERVER, $var, FILTER_SANITIZE_URL);
//        return filter_var($_SERVER[$var], FILTER_SANITIZE_URL);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter sanitize Session
    |--------------------------------------------------------------------------
    */
    public static function filterSession($key)
    {
        return @filter_var($_SESSION[$key], FILTER_SANITIZE_ENCODED);
    }

    public static function filterSessionString($key)
    {
        return @filter_var($_SESSION[$key], FILTER_SANITIZE_STRING);
    }

    public static function filterSessionInt($key)
    {
        return @filter_var($_SESSION[$key], FILTER_SANITIZE_NUMBER_INT);
    }

    /*
     |--------------------------------------------------------------------------
     | Filter sanitize Cookie
     |--------------------------------------------------------------------------
     */
    public static function filterCookie($name)
    {
        if (!isset($_COOKIE[$name])) {
            return "";
        }
        return filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_ENCODED);
    }

    public static function filterCookieInt($name)
    {
        if (!isset($_COOKIE[$name])) {
            return "";
        }
        return filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function filterCookieVarString($name)
    {
        if (!isset($_COOKIE[$name])) {
            return "";
        }
        return filter_var($_COOKIE[$name], FILTER_SANITIZE_STRING);
    }

    public static function filterCookieVarInt($name)
    {
        if (!isset($_COOKIE[$name])) {
            return "";
        }
        return filter_var($_COOKIE[$name], FILTER_SANITIZE_NUMBER_INT);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Array
    |--------------------------------------------------------------------------
    */
    public static function filterArray($arr)
    {
        return filter_var_array($arr);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter sanitize var
    |--------------------------------------------------------------------------
    */
    public static function filterVarUnsafe($var)
    {
        return filter_var($var, FILTER_UNSAFE_RAW);
    }

    //must like "example.php?name=Peter&age=37"
    public static function filterVarUrlWithUri($var)
    {
        return filter_var($var, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED);
    }

    public static function filterVarUrl($var)
    {
        return filter_var($var, FILTER_SANITIZE_URL);
    }

    /* Removes special characters  "Is Peter <smart>" in view source see Is Peter &lt;smart&gt;*/
    public static function filterVarSpecialChar($var)
    {
        return filter_var($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /* The FILTER_SANITIZE_STRING filter removes tags and remove or encode special characters from a string. */
    public static function filterVarString($var)
    {
        return filter_var($var, FILTER_SANITIZE_STRING);
    }

    public static function filterVarInt($var)
    {
        return filter_var($var, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function filterVarFloat($var)
    {
        return filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT);
    }

    /* Encode special characters in the $url variable: */
    public static function filterVarEncode($var)
    {
        return filter_var($var, FILTER_SANITIZE_ENCODED);
    }

    /* Apply addslashes() to single quote ('),double quote ("),backslash (\),NULL */
    public static function filterVarAddSlahes($var)
    {
        return filter_var($var, FILTER_SANITIZE_MAGIC_QUOTES);
    }

    //"john(.doe)@exa//mple.com" Remove all illegal characters from an email address john.doe@example.com
    public static function filterVarEmail($var)
    {
        return filter_var($var, FILTER_SANITIZE_EMAIL);
    }

    /*
     |--------------------------------------------------------------------------
     | Filter validate Variable
     |--------------------------------------------------------------------------
    */
    public static function isValidBoolean($var)
    {

        $var = (string)$var;
        $var = strtolower($var);
//        if (!filter_var($var, FILTER_VALIDATE_BOOLEAN) === false) {
//            return true;
//        }
//        return false;
        return $var === 1 || $var === 0 || $var === '1' || $var === '0' || $var === 'true' || $var === 'false';
    }

    public static function isValidFloat($var)
    {
        if (!filter_var($var, FILTER_VALIDATE_FLOAT) === false) {
            return true;
        }
        return false;
    }

    public static function isNumeric($var)
    {
        return is_numeric($var);
    }

    public static function isValidInt($var)
    {
        if ($var == '0') {
            return true;
        }
        if (!filter_var($var, FILTER_VALIDATE_INT) === false) {
            return true;
        }
        return false;
    }

    public static function isNumberFormat($num, $position = 0)
    {
        if ($num) {
            return number_format($num, $position);
        }
        return false;
    }

    public static function isValidIp($var)
    {
        if (!filter_var($var, FILTER_VALIDATE_IP) === false) {
            return true;
        }
        return false;
    }

    public static function isValidUrl($var)
    {
        $var = filter_var($var, FILTER_SANITIZE_URL);
        if (!filter_var($var, FILTER_VALIDATE_URL) === false) {
            return true;
        }
        return false;
    }

    public static function isValidEmail($var)
    {
        if (!filter_var($var, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        }
        return false;
    }

    public static function isPhoneNo($phone)
    {
        if (preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $phone)) {
            return true;
        }
        return false;
    }

    public static function isValidUsername($var)
    {//Must start with letter,6-32 characters, Allow Letters numbers _
        if (preg_match('/^[A-Za-z][A-Za-z0-9_]{5,31}$/', $var)) {
            return true;
        }
        return false;
    }

    public static function isValidateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /*
    |--------------------------------------------------------------------------
    | Quick Filter And Clean
    |--------------------------------------------------------------------------
    */
    public static function cleanPasswrod($value)
    {


        $value = trim($value); //remove empty spaces
        $value = strip_tags($value); //remove html tags
        $value = htmlentities($value, ENT_QUOTES, 'UTF-8'); //for major security transform some other chars into html corrispective...

        return $value;
    }

    public static function cleanText($value)
    {

        $value = trim($value); //remove empty spaces
        $value = strip_tags($value); //remove html tags
        $value = filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES); //addslashes();
        $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW); //remove /t/n/g/s
        $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); //remove é à ò ì ` ecc...
        $value = htmlentities($value, ENT_QUOTES, 'UTF-8'); //for major security transform some other chars into html corrispective...

        return $value;
    }

    public static function cleanEmail($value)
    {

        $value = trim($value); //remove empty spaces
        $value = strip_tags($value); //remove html tags
        $value = filter_var($value, FILTER_SANITIZE_EMAIL); //e-mail filter;
        if ($value = filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value = htmlentities($value, ENT_QUOTES, 'UTF-8');//for major security transform some other chars into html corrispective...
        } else {
            $value = "BAD";
        }
        return $value;
    }
}