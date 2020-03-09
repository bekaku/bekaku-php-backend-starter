<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 31/7/2019
 * Time: 9:45 AM
 */

namespace application\util;


class RestApi
{
    public static function callApi($url, $params = array(), $methodType = SystemConstant::METHOD_GET, $header = null)
    {
        /*
         *
        $param = array(
            "username" => "test"
        ); // data u want to post


        $u = MessageUtils::getConfig('thaipost_username');
        $p = MessageUtils::getConfig('thaipost_password');
        $header=array(
            "Content-Type: application/json; charset=utf-8",
            "Accept: application/json",
            "Authorization: Basic ".base64_encode($u.':'.$p)
        );
        $data = ControllerUtil::callApi($url, $param, SystemConstant::METHOD_GET, $header);
         */

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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header ? $header : $defaultHeader);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methodType);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$password);

        $errors = curl_error($ch);
        $result = curl_exec($ch);

        $data = json_decode($result, true);
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response[SystemConstant::SERVER_STATUS_CODE_ATT] = $returnCode;
        $response[SystemConstant::SERVER_MSG_ATT] = self::getHttpStatusMessage($returnCode);
        $response['errorMessage'] = $errors;
        $response[SystemConstant::SERVER_RESPONSE_DATA] = $data;
        return $response;
    }

    public static function getHttpStatusMessage($status)
    {
        $msg = $status ? i18next::getTranslation('httpStatus.' . $status) : null;
        return $msg;
    }

    public static function callRestApiGetContent($url, $params = array(), $methodType = SystemConstant::METHOD_GET, $header = null)
    {
//        $params = array(
//            '_classDate' => '01/11/2561',
//            'xxx' => '01/11/2561',
//        );
        $defaultHeader = array(
            SystemConstant::CONTENT_TYPE_APPLICATION_URLENCODED,
            SystemConstant::ACCECP_CONTENT_TYPE_APPLICATION_JSON
        );
        $options = array(
            'http' => array(
                'header' => $header ? $header : $defaultHeader,
                'method' => $methodType,
                'content' => http_build_query($params),
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

    public static function callRestApiNormal($url, $params = array(), $methodType = SystemConstant::METHOD_GET)
    {
        $curl = curl_init();
        switch ($methodType) {
            case "POST":
            case "GET":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($params)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }


    //
    public static function callApiStd($url, $params = array(), $methodType = SystemConstant::METHOD_GET, $header=null){


        $response = null;
//        $data = array(// data u want to post
//            "username" => "test"
//        );

        $defaultHeader=array(
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

        $errors = curl_error($ch);
        $result = curl_exec($ch);

        $data = json_decode($result, true);
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response[SystemConstant::SERVER_STATUS_CODE_ATT]=$returnCode;
        $response[SystemConstant::SERVER_MSG_ATT]=self::getHttpStatusMessage($returnCode);;
        $response['errorMessage']=$errors;
        $response[SystemConstant::SERVER_RESPONSE_DATA]=$data;
        return $response;
    }
}