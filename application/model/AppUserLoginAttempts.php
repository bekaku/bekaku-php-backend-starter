<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppUserLoginAttempts extends BaseModel
{
    public static $tableName = 'app_user_login_attempts';

    private $app_user;
    private $time;
    private $ip_address;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'app_user' => self::TYPE_STRING,
            'time' => self::TYPE_STRING,
            'ip_address' => self::TYPE_STRING,
            'created_date' => self::TYPE_DATE_TIME,
            'id' => self::TYPE_AUTO_INCREMENT,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'app_user' => self::TYPE_STRING,
            'time' => self::TYPE_STRING,
            'ip_address' => self::TYPE_STRING,

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
     * @return mixed
     */
    public function getAppUser()
    { 
        return $this->app_user;
    }

    /**
     * @param mixed $app_user
     */
    public function setAppUser($app_user)
    {
        $this->app_user = $app_user;
    }

    /**
     * @return mixed
     */
    public function getTime()
    { 
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    { 
        return $this->ip_address;
    }

    /**
     * @param mixed $ip_address
     */
    public function setIpAddress($ip_address)
    {
        $this->ip_address = $ip_address;
    }

}