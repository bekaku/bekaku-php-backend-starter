<?php

/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 29/12/2015
 * Time: 10:30 AM
 */

namespace application\controller;

use application\core\AppController;
use application\util\FilterUtils;
use application\util\JWT;
use application\util\SystemConstant;
use application\util\RestApi;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class TestController extends AppController
{
    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
    }

    public function index()
    {
        $this->testSendMail();
    }

    private function testSendMail()
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'cpanel01mh.bkk1.cloud.z.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'chanavee@ats.co.th';                     //SMTP username
            $mail->Password   = '';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('chanavee@ats.co.th', 'Mailer');
            $mail->addAddress('baekaku@gmail.com', 'Joe User');     //Add a recipient
            $mail->addAddress('chana_vb@yahoo.com');               //Name is optional
            $mail->addReplyTo('chanavee@ats.co.th', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    private function testSendLineNoti()
    {
        $this->sendNotify([
            'message' => 'ส่งข้อความพร้อมรูปภาพ',
            'imageThumbnail' => 'https://fastly.picsum.photos/id/866/240/240.jpg?hmac=1-kEHESN2M0RdXKLwe1esbZk7zUhVNrW8Tvg8IRnZWs',
            'imageFullsize' => 'https://fastly.picsum.photos/id/866/600/800.jpg?hmac=7MjLDCug0s7JWRVrJz0nn-YuyW4PezXyfryEaOTgJx0',
        ]);
    }
    private function sendNotify($queryData)
    {
        $postData = http_build_query($queryData, '', '&');
        return RestApi::callApiV2(
            'https://notify-api.line.me/api/notify',
            null,
            SystemConstant::METHOD_POST,
            [
                'Content-type: application/x-www-form-urlencoded',
                'Authorization: Bearer 3p0acbWfinxF2CVxS2XeCuHqczFkM2FekZc7Y3pdXrH'
            ],
            false,
            $postData
        );
    }
    private function env($key = null)
    {
        if (!$key) {
            return null;
        }
        $parsed = parse_ini_file(__SITE_PATH . '/.env');
        //        if (isset($parsed[$key])) {
        //            return $parsed[$key];
        //        }
        return $parsed;
    }

    private function testEncodeJWT($secretServerkey)
    {
        $payload = array([
            SystemConstant::API_NAME_ATT => 'ctt_logistic',
        ]);

        //        $payload = array([
        //            'uid' => 1,
        //            'key' => '020480883423d36cead213f6cafab6d487ecece1c02f1b084afe281b5197b6f12ced0f90be75be9b513da35a89e2f6c19c42fab62aa8a349bdd00940fec92939'
        //        ]);
        $jwt = JWT::encode($payload, $secretServerkey);

        //        $data['payloadEncode']=$payload;
        // Create token header as a JSON string
        echoln('jwt > ' . $jwt);

        $jwtDecode = JWT::decode($jwt, $secretServerkey, true);
        //        $data['jwtDecode'] = $jwtDecode;
        //        echoln($payLoad[SystemConstant::API_NAME_ATT]);
        //        print_r($payLoad);
        //        print_r($jwtDecode);

        echo json_encode($payload);
        return $jwt;
    }

    public function testGetMultiParam()
    {
        $module = FilterUtils::filterGetString('module');
        $module_param2 = FilterUtils::filterGetString('module_param2');
        echoln('$module=>' . $module . ', $module_param2=>' . $module_param2);
    }

    public function edrPhpIndex()
    {
        echoln("edrPhpIndex");
    }

    private function readFileFromFolder()
    {
        $somePath = 'D:\studentPicture\appuser';
        $dir = opendir($somePath);

        //looping through filenames
        while (false !== ($file = readdir($dir))) {
            echo "$file\n";
        }
    }

    private function findWhere($array, $matching)
    {
        foreach ($array as $item) {
            $is_match = true;
            foreach ($matching as $key => $value) {

                if (is_object($item)) {
                    if (!isset($item->$key)) {
                        $is_match = false;
                        break;
                    }
                } else {
                    if (!isset($item[$key])) {
                        $is_match = false;
                        break;
                    }
                }

                if (is_object($item)) {
                    if ($item->$key != $value) {
                        $is_match = false;
                        break;
                    }
                } else {
                    if ($item[$key] != $value) {
                        $is_match = false;
                        break;
                    }
                }
            }

            if ($is_match) {
                return $item;
            }
        }

        return false;
    }

    private function arrayTest()
    {
        $data = array(
            array("firstname" => "Mary", "lastname" => "Johnson", "age" => 25), //key 0
            array("firstname" => "Amanda", "lastname" => "Miller", "age" => 18), //key 1
            array("firstname" => "James", "lastname" => "Brown", "age" => 31), //key 2
            array("firstname" => "Patricia", "lastname" => "Williams", "age" => 7), //key 3
            array("firstname" => "Michael", "lastname" => "Davis", "age" => 43), //key 4
            array("firstname" => "Sarah", "lastname" => "Miller", "age" => 24), //key 5
            array("firstname" => "Patrick", "lastname" => "Miller", "age" => 27) //key 6
        );
        $phpMinimumVersion = '5.5.0';

        if (phpversion() >= (float)$phpMinimumVersion) {


            $key = array_search('Michael', array_column($data, 'firstname'));
            //return key = 4 because Michael is record 5
            print_r($data[$key]);
        }

        //        $first_names = array_column($data, 'firstname');
        //        print_r($first_names);

        //        foreach($data as $v){
        //            echoln('firstname='.$v['firstname'].' age='.$v['age']);
        //        }
    }
}
