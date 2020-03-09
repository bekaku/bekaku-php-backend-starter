<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 25/12/2015
 * Time: 3:03 PM
 */

namespace application\service;
use application\core\DatabaseSupport;
use application\serviceInterface\LoginServiceInterface as LoginServiceInterface;
use application\util\AppUtil;
use application\util\ControllerUtil as ControllerUtil;
use application\util\FilterUtils as FilterUtil;
use application\util\AppUtil as AppUtils;
use application\util\DateUtils as DateUtil;
use application\util\FilterUtils;

class LoginService extends DatabaseSupport implements LoginServiceInterface
{
    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }

    public function login($userName, $password, $isRemember=false)
    {
        $query = "SELECT id, username, login_password, salt
					  FROM app_user WHERE username=:username LIMIT 1";


        $this->query($query);
        $this->bind(":username", $userName);
        $userData = $this->single();
        if($this->rowCount() == 1){
            $userIdInDb = $userData['id'];

            if(!ControllerUtil::isPermissionByUserId($this->getDbh(),$userIdInDb,'login')){
                ControllerUtil::setErrorMessage('No Permission For Login');
                return false;
            }

            $userUsernameInDb = $userData['username'];
            $userSaltInDb = $userData['salt'];
            $userPasswordInDb = $userData['login_password'];
            // hash the password with the unique salt.
//            $password = hash('sha512', $password . $userSaltInDb);
            $password = ControllerUtil::genHashPassword($password, $userSaltInDb);
            if ($this->checkBrute($userIdInDb)==true) {
                // Account is locked
                // Send an email to user saying their account is locked
                ControllerUtil::setErrorMessage('Your account is locked');
                return false;
            }else{
                // Check if the password in the database matches
                // the password the user submitted.
                if ($userPasswordInDb == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = FilterUtil::filterServer('HTTP_USER_AGENT');//$_SERVER['HTTP_USER_AGENT'];

                    // XSS protection as we might print this Int value
                    // preg_replace("/[^0-9]+/", "", $userIdInDb);
                    $userIdInDb =  FilterUtil::filterVarInt($userIdInDb);
                    $_SESSION['user_id'] = $userIdInDb;

                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $userUsernameInDb);

                    $loginStringHash = hash('sha512', $password . $user_browser);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = $loginStringHash;

                    //set to cookie if user want to remember
                    if($isRemember){
                        ControllerUtil::setCookieByName('user_id', $userIdInDb);
                        ControllerUtil::setCookieByName('username', $username);
                        ControllerUtil::setCookieByName('login_string', $loginStringHash);
                    }

                    //insert to app_user_login
                    $query = "INSERT INTO app_user_login (loged_in_date, loged_ip, app_user )
                              VALUES (:loged_in_date, :loged_ip, :app_user)";
                    $this->query($query);
                    $this->bind(":loged_in_date", DateUtil::getDateNow());
                    $this->bind(":loged_ip", AppUtils::getRealIpAddr());
                    $this->bind(":app_user", $userIdInDb);
                    $this->execute();

                    // Login successful.
                    return true;
                }else{
                    // Password is not correct
                    // We record this attempt in the database
//                    $now = time();
                    $query = "INSERT INTO app_user_login_attempts (app_user, time, ip_address, created_date )
                              VALUES (:app_user, :timeNow, :ip_address,  :created_date)";
                    $this->query($query);
                    $this->bind(":app_user", $userIdInDb);
                    $this->bind(":timeNow", DateUtil::getTimeNow());
                    $this->bind(":ip_address", AppUtils::getRealIpAddr());
                    $this->bind(":created_date", DateUtil::getDateNow());
                    $this->execute();
                }
            }

        }else{
            return false;
        }
        return false;

    }
    private function checkBrute($user_id){

        // Get timestamp of current time
        $now = time();
        // All login attempts are counted from the past 2 hours.
        $valid_attempts = $now - (2 * 60 * 60);
        $query = "SELECT time FROM app_user_login_attempts WHERE app_user = :id AND time > '$valid_attempts'";
        $this->query($query);
        $this->bind(":id", $user_id);
        $this->execute();
        if($this->rowCount()>5){
            return true;
        }else{
            return false;
        }

    }
    public function isLogined()
    {
        $userId = FilterUtil::filterCookieVarInt('user_id') ? FilterUtil::filterCookieVarInt('user_id') : FilterUtil::filterSessionInt('user_id');
        $loginString = FilterUtil::filterCookieVarString('login_string') ? FilterUtil::filterCookieVarString('login_string') : FilterUtil::filterSessionString('login_string');
        $username = FilterUtil::filterCookieVarString('username') ? FilterUtil::filterCookieVarString('username') : FilterUtil::filterSessionString('username');


        // Check if all session variables are set
          if($userId && $username && $loginString)
          {

            $userData = null;

//            $userId = FilterUtil::filterSessionInt('user_id');
//            $userName = FilterUtil::filterSession('username');
//            $loginString = FilterUtil::filterSessionString('login_string');

            // Get the user-agent string of the user.
            $userBrowser = FilterUtil::filterServer('HTTP_USER_AGENT');// $_SERVER['HTTP_USER_AGENT'];

            $query = "SELECT login_password FROM app_user WHERE id=:id LIMIT 1";
            $this->query($query);
            $this->bind(":id", $userId);
            $userData = $this->single();

            if($this->rowCount() == 1){
                $userPassword = $userData['login_password'];
                $loginCheckString = hash('sha512', $userPassword.$userBrowser);
                if($loginCheckString == $loginString){
                    //loged in
                    return true;
                }else{
                    return false;
                }
            }

        }else{
            return false;
        }

        return false;
    }
    public function logout()
    {
        // Unset all session values
        $_SESSION = array();
        // get session parameters
        $params = session_get_cookie_params();
        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

        if(isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 7000000, '/');
        }

        if(isset($_COOKIE['user_id'])){
            setcookie('user_id', '', time()-7000000, '/');
        }
        if(isset($_COOKIE['username'])){
            setcookie('username', '', time()-7000000, '/');
        }
        if(isset($_COOKIE['login_string'])){
            setcookie('login_string', '', time()-7000000, '/');
        }

        // Destroy session
        session_destroy();
        return true;
    }
}