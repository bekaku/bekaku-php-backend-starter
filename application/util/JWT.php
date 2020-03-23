<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 11/9/2018
 * Time: 2:39 PM
 */

namespace application\util;

use DomainException;
use UnexpectedValueException;


/**
 * JSON Web Token implementation
 *
 * Minimum implementation used by Realtime auth, based on this spec:
 * http://self-issued.info/docs/draft-jones-json-web-token-01.html.
 *
 * @author Neuman Vong <neuman@twilio.com>
 */
class JWT
{
    /**
     * @param string $jwt The JWT
     * @param string|null $key The secret key
     * @param bool $verify Don't skip verification process
     *
     * @return object The JWT's payload as a PHP object
     */
    public static function decode($jwt, $key = null, $verify = true)
    {
        $tks = explode('.', $jwt);
        $msg = '';
        $isSign = true;
        $payload = null;
        if (count($tks) != 3) {
//            throw new UnexpectedValueException('Wrong number of segments');

            $msg .= 'Wrong number of segments <br>';
            $isSign = false;
        } else {
            list($headb64, $payloadb64, $cryptob64) = $tks;
            if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))
            ) {
//            throw new UnexpectedValueException('Invalid segment encoding');
                $msg .= 'Invalid segment encoding <br>';
                $isSign = false;
            }

            if ($isSign) {

                if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($payloadb64))
                ) {
//            throw new UnexpectedValueException('Invalid segment encoding');
                    $msg .= 'Invalid segment encoding <br>';
                    $isSign = false;
                } else {
                    $isSign = true;
                }
            }

            if ($isSign) {
                $sig = JWT::urlsafeB64Decode($cryptob64);
                if ($verify) {
                    if (empty($header->alg)) {
//                throw new DomainException('Empty algorithm');
                        $msg .= 'JWT Empty algorithm <br>';
                        $isSign = false;
                    }
                    if ($sig != JWT::sign("$headb64.$payloadb64", $key, $header->alg)) {
//                throw new UnexpectedValueException('Signature verification failed');
                        $msg .= 'JWT Signature verification failed';
                        $isSign = false;
                    }
                }
            }
        }

        if (!$isSign) {
            $data["status"] = $isSign;
            $data["msg"] = $msg;
            $data["payload"] = null;
        } else {

            $data["status"] = $isSign;
            $data["msg"] = $msg;
//            $dataPayLoad = [];
//            foreach ($payload as $key => $value) {
//                $dataPayLoad[$key] = $value;
//            }
//            print_r($dataPayLoad);
//            $data["payload"] = count($dataPayLoad) > 0 ? $dataPayLoad[0] : $dataPayLoad;
            $data["payload"] = is_array($payload) && count($payload) > 0 ? $payload[0] : $payload;
        }
        return $data;
    }

    private static function objectToArray($d)
    {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        } else {
            // Return array
            return $d;
        }
    }

    /**
     * @param object|array $payload PHP object or array
     * @param string $key The secret key
     * @param string $algo The signing algorithm
     *
     * @return string A JWT
     */
    public static function encode($payload, $key, $algo = 'HS256')
    {
        $header = array('typ' => 'JWT', 'alg' => $algo);
        $segments = array();
        $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
        $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
        $signing_input = implode('.', $segments);
        $signature = JWT::sign($signing_input, $key, $algo);
        $segments[] = JWT::urlsafeB64Encode($signature);
        return implode('.', $segments);
    }

    /**
     * @param string $msg The message to sign
     * @param string $key The secret key
     * @param string $method The signing algorithm
     *
     * @return string An encrypted message
     */
    public static function sign($msg, $key, $method = 'HS256')
    {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    /**
     * @param string $input JSON string
     *
     * @return object Object representation of JSON string
     */
    public static function jsonDecode($input)
    {
        $obj = json_decode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::handleJsonError($errno);
        } else if ($obj === null && $input !== 'null') {
            throw new DomainException('Null result with non-null input');
        }
        return $obj;
    }

    /**
     * @param object|array $input A PHP object or array
     *
     * @return string JSON representation of the PHP object or array
     */
    public static function jsonEncode($input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::handleJsonError($errno);
        } else if ($json === 'null' && $input !== null) {
            throw new DomainException('Null result with non-null input');
        }
        return $json;
    }

    /**
     * @param string $input A base64 encoded string
     *
     * @return string A decoded string
     */
    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param string $input Anything really
     *
     * @return string The base64 encode of what you passed in
     */
    public static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * @param int $errno An error number from json_last_error()
     *
     * @return void
     */
    private static function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        throw new DomainException(isset($messages[$errno])
            ? $messages[$errno]
            : 'Unknown JSON error: ' . $errno
        );
    }


    public static function defaultJWTHeaderHS256()
    {

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        return $base64UrlHeader;
    }
}