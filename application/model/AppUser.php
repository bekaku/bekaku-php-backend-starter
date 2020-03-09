<?php
namespace application\model;

use application\core\AppModel;
use application\util\UploadUtil;
use application\util\DateUtils;
class AppUser extends AppModel
{
    public static $tableName = 'app_user';

    private $username;
    private $email;
    private $login_password;
    private $salt;
    private $status = true;
    private $img_name;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'username' => self::TYPE_STRING,
            'email' => self::TYPE_STRING,
            'login_password' => self::TYPE_STRING,
            'salt' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,
            'img_name' => self::TYPE_STRING,
            'created_user' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'username' => self::TYPE_STRING,
            'email' => self::TYPE_STRING,
//            'login_password' => self::TYPE_STRING,
//            'salt' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,
            'img_name' => self::TYPE_STRING,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
        ));

        /* init optional field*/
        $this->setTableOptionalField(array(
            //'field_name_option',
        ));

        $this->populate($data, $this);
        $this->populateBase($data);
    }

    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * @return string
     */
    public function getUsername()
    { 
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    { 
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getLoginPassword()
    { 
        return $this->login_password;
    }

    /**
     * @param mixed $login_password
     */
    public function setLoginPassword($login_password)
    {
        $this->login_password = $login_password;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    { 
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getImgName()
    { 
        return $this->img_name;
    }

    /**
     * @param string $img_name
     */
    public function setImgName($img_name)
    {
        $this->img_name = $img_name;
    }

    /**
     * @return bool|string
     */
    public function getImgNameThumbnail()
    {
        return UploadUtil::displayAvatarThumnailPubic($this->getImgName(),$this->getCreatedDate());
    }
}