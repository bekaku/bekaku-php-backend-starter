<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 21/3/2019
 * Time: 2:51 PM
 */

namespace application\service;

use application\core\DatabaseSupport;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;

class AuthenService extends DatabaseSupport
{
    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function userAuthenApi($userName, $password, $newSalt = false)
    {

        $dataReturn[SystemConstant::SERVER_STATUS_ATT] = false;
        $dataReturn[SystemConstant::USER_API_KEY_ATT] = null;
        $dataReturn[SystemConstant::SERVER_MSG_ATT] = i18next::getTranslation('error.err_username_or_passwd_notfound');

        $loginKeyHash = null;
        $query = "SELECT id, username, login_password, salt
					  FROM app_user WHERE username=:username LIMIT 1";
        $this->query($query);
        $this->bind(":username", $userName);
        $userData = $this->single();
        if ($this->rowCount() == 1) {

            $userIdInDb = $userData['id'];
            $userSaltInDb = $userData['salt'];
            $hashPasswordInDb = $userData['login_password'];
            if ($this->checkBrute($userIdInDb) == true) {
                $dataReturn[SystemConstant::SERVER_MSG_ATT] = i18next::getTranslation('error.accountLocked');
            } else {
                $inputHashPassword = ControllerUtil::genHashPassword($password, $userSaltInDb);
                // Check if the password in the database matches the password the user submitted.
                if ($inputHashPassword == $hashPasswordInDb) {
                    //update user logined to db
                    $this->updateUserLogin($userIdInDb);
                    //generate new salt if required
                    if($newSalt){
                        $hashPasswordInDb = $this->userUpdateSalt($password, $userIdInDb);
                    }
                    // Get the user-agent string of the user. for apiKey
                    $userAgent = FilterUtils::filterServer('HTTP_USER_AGENT');
                    $dataReturn[SystemConstant::USER_API_KEY_ATT] = ControllerUtil::genHashPassword($hashPasswordInDb, $userAgent);
                    $dataReturn[SystemConstant::SERVER_MSG_ATT] = null;
                    $dataReturn[SystemConstant::SERVER_STATUS_ATT] = true;
                } else {
                    $this->updateLoginFail($userIdInDb);
                }
            }
        }

        return $dataReturn;
    }

    private function userUpdateSalt($loginPwd, $userId)
    {
        $randomSalt = ControllerUtil::getRadomSault();
        $pwdHash = ControllerUtil::genHashPassword($loginPwd, $randomSalt);
        $status = $this->updateHelper('app_user', array(
            'salt' => $randomSalt, 'login_password' => $pwdHash),
            array('id' => $userId),
            'AND');
        if ($status) {
            return $pwdHash;
        }
        return null;
    }

    private function checkBrute($user_id)
    {

        // Get timestamp of current time
        $now = time();
        // All login attempts are counted from the past 2 hours.
        $valid_attempts = $now - (2 * 60 * 60);
        $query = "SELECT time FROM app_user_login_attempts WHERE app_user = :id AND time > '$valid_attempts'";
        $this->query($query);
        $this->bind(":id", $user_id);
        $this->execute();
        if ($this->rowCount() > 5) {
            return true;
        } else {
            return false;
        }

    }

    private function updateUserLogin($userIdInDb)
    {

        //insert to app_user_login
        $query = "INSERT INTO app_user_login (loged_in_date, loged_ip, app_user )
                              VALUES (:loged_in_date, :loged_ip, :app_user)";
        $this->query($query);
        $this->bind(":loged_in_date", DateUtils::getDateNow());
        $this->bind(":loged_ip", AppUtil::getRealIpAddr());
        $this->bind(":app_user", $userIdInDb);
        $this->execute();
    }

    private function updateLoginFail($userIdInDb)
    {
        // Password is not correct
        // We record this attempt in the database
        $query = "INSERT INTO app_user_login_attempts (app_user, time, ip_address, created_date )
                              VALUES (:app_user, :timeNow, :ip_address,  :created_date)";
        $this->query($query);
        $this->bind(":app_user", $userIdInDb);
        $this->bind(":timeNow", DateUtils::getTimeNow());
        $this->bind(":ip_address", AppUtil::getRealIpAddr());
        $this->bind(":created_date", DateUtils::getDateNow());
        $this->execute();
    }

    public function checkUserAuthenApi($uid, $loginToken)
    {
        if ($uid && $loginToken) {
            $query = "SELECT login_password FROM app_user WHERE id=:id LIMIT 1 ";
            $this->query($query);
            $this->bind(":id", $uid);
            $userData = $this->single();
            if ($this->rowCount() == 1) {
                $loginKeyCheckHash = ControllerUtil::genHashPassword($userData['login_password'], FilterUtils::filterServer('HTTP_USER_AGENT'));
                if ($loginKeyCheckHash == $loginToken) {
                    //loged in
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}