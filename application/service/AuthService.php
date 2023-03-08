<?php


namespace application\service;


use application\core\BaseDatabaseSupport;
use application\util\AppUtil;
use application\util\ControllerUtil;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use stdClass;

class AuthService extends BaseDatabaseSupport
{

    /**
     * @var UserService
     */
    private $userService;

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
        $this->userService = new UserService($this->getDbh());

    }

    public function signin($email, $password, $newSalt = false)
    {

        $result = new stdClass();
        $result->status = false;
        $result->apiKey = null;
        $result->message = i18next::getTranslation('error.err_username_or_passwd_notfound');

        $loginKeyHash = null;
//        $query = "SELECT id, username, login_password, salt
//					  FROM user WHERE username=:username LIMIT 1";
//        $this->query($query);
//        $this->bind(":username", $userName);

        $userData = $this->userService->findForAuthenByEmail($email);

        if ($this->userService->rowCount() == 1) {
            $userIdInDb = $userData->id;
            $userSaltInDb = $userData->salt;
            $hashPasswordInDb = $userData->password;

            if ($this->checkBrute($userIdInDb) == true) {
                $result->message = i18next::getTranslation('error.accountLocked');
            } else {
                $inputHashPassword = ControllerUtil::genHashPassword($password, $userSaltInDb);

                // Check if the password in the database matches the password the user submitted.
                if ($inputHashPassword == $hashPasswordInDb) {
                    //update user logined to db
                    $this->updateUserLogin($userIdInDb);
                    //generate new salt if required
                    if ($newSalt) {
                        $this->userUpdateSalt($password, $userIdInDb);
                    }
                    // Get the user-agent string of the user. for apiKey
                    $hashUserDescription = ControllerUtil::genHashPassword(FilterUtils::filterServer('HTTP_USER_AGENT'), $userIdInDb . DateUtils::getTimeNow());
                    $result->apiKey = ControllerUtil::genHashPassword(ControllerUtil::getRadomSault(), $hashUserDescription);
                    $result->message = i18next::getTranslation('success.loginSuccess');
                    $result->status = true;

                } else {
                    $this->updateLoginFail($userIdInDb);
                }
            }
        }

        return $result;
    }

    private function userUpdateSalt($loginPwd, $userId)
    {
        $randomSalt = ControllerUtil::getRadomSault();
        $pwdHash = ControllerUtil::genHashPassword($loginPwd, $randomSalt);
        $status = $this->updateHelper(
            'user',
            ['salt' => $randomSalt, 'password' => $pwdHash],
            ['id' => $userId],
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
        $query = "SELECT `time` FROM user_login_attempts WHERE `user` = :id AND `time` > '$valid_attempts'";
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
        $query = "INSERT INTO user_login_log (loged_in_date, loged_ip, `user` )
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
        $query = "INSERT INTO user_login_attempts (`user`, `time`, ip_address, created_date )
                              VALUES (:app_user, :timeNow, :ip_address,  :created_date)";
        $this->query($query);
        $this->bind(":app_user", $userIdInDb);
        $this->bind(":timeNow", DateUtils::getTimeNow());
        $this->bind(":ip_address", AppUtil::getRealIpAddr());
        $this->bind(":created_date", DateUtils::getDateNow());
        $this->execute();
    }
}