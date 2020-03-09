<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 14/8/2018
 * Time: 11:08 AM
 */

namespace application\util;


use application\model\MailContent;
use application\util\PHPMailer\Exception;
use application\util\PHPMailer\PHPMailer;

class MailUtil
{

    public static function sendGmail(MailContent $content){


        if(!empty($content)){
//            echoln('getFromMail='.$content->getFromMail());
//            echoln('getFromName='.$content->getFromName());
//            echoln('getReplyTo='.$content->getReplyTo());
//            echoln('getReplyToName='.$content->getReplyToName());
//            echoln('getAddress');
//            $addressList = $content->getAddress();
//            if(count($addressList)>0){
//                foreach ($addressList AS $address){
//                    echoln('address='.$address['address'].', name='.$address['name']);
//                }
//            }
//            echoln('isHtml='.$content->isHtml());
//            echoln('getSubject='.$content->getSubject());
//            echoln('getBody='.$content->getBody());

            $gmail = MessageUtils::getConfig('gmail');
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            try {
                //Server settings
                /**/
                $mail->SMTPDebug = 0;                                 // 2 Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->CharSet="UTF-8";
                $mail->isSMTP();
                $mail->Host = $gmail['host'];  //gmail SMTP server
                $mail->SMTPAuth = $gmail['smtp_auth'];
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ));
                $mail->Username = $gmail['username'];                 // SMTP username
                $mail->Password = $gmail['password'];                           // SMTP password
                $mail->SMTPSecure = $gmail['smtp_secure'];
                $mail->Port = $gmail['port'];                    //SMTP port


                //Recipients
                $mail->setFrom($content->getFromMail(), $content->getFromName());
                $mail->addReplyTo($content->getReplyTo(), $content->getReplyToName());

                $addressList = $content->getAddress();
                if(count($addressList)>0){
                    foreach ($addressList AS $address){
                        $mail->addAddress($address['address'], $address['address']);     // Add a recipient
                    }
                }

                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');

                //Attachments
                //$mail->addAttachment('files/2.rar');         // Add attachments
                //$mail->addAttachment('files/1.png', 'new.jpg');    // Optional name

                //Content
                $mail->isHTML($content->isHtml());// Set email format to HTML
                $mail->Subject = $content->getSubject();
                $mail->Body    = $content->getBody();
//                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
//                echo 'Message has been sent';
            } catch (Exception $e) {
//                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            }
        }
    }
    private function sendGmailTest(){



        $gmail = new MailContent();
        $gmail->setFromMail('wct.thaioil@gmail.com');
        $gmail->setFromName('Warehouse Contents Tracking');
        $gmail->setReplyTo('wct.thaioil@gmail.com');
        $gmail->setReplyToName('No Reply');

        $gmail->setAddress(
            array(
                array(
                    'address' => 'chana_vb@yahoo.com',
                    'name' => 'Chanavee'
                ),
                array(
                    'address' => 'baekaku@gmail.com',
                    'name' => 'baekaku'
                )
            )
        );
        $gmail->setSubject('Notification Unloading Manual Changed');
        $gmail->setBody('Body Notification Unloading Manual Changed');
        MailUtil::sendGmail($gmail);

        /*
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->CharSet="UTF-8";
            $mail->isSMTP();
            $mail->Host = 'smtp.googlemail.com';  //gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ));
            $mail->Username = 'wct.thaioil@gmail.com';                 // SMTP username
            $mail->Password = 'fFas251@';                           // SMTP password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;                    //SMTP port

            //Recipients
            $mail->setFrom('wct.thaioil@gmail.com', 'Warehouse Contents Tracking');
            $mail->addReplyTo('wct.thaioil@gmail.com', 'No Reply');
            $mail->addAddress('chana_vb@yahoo.com', 'Chanavee');     // Add a recipient
            $mail->addAddress('baekaku@gmail.com');               // Name is optional
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('files/2.rar');         // Add attachments
            //$mail->addAttachment('files/1.png', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Here is the subject ทดสอบการส่งเมล์นะ';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b> ทดสอบการส่งเมล์นะ';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
        */
    }

}