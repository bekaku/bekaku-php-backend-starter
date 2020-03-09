<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppUserOnline extends BaseModel
{
    public static $tableName = 'app_user_online';

    private $sessions;
    private $app_user;
    private $times;
    private $ip_address;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'sessions' => self::TYPE_STRING,
            'app_user' => self::TYPE_STRING,
            'times' => self::TYPE_STRING,
            'ip_address' => self::TYPE_STRING,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'sessions' => self::TYPE_STRING,
            'app_user' => self::TYPE_STRING,
            'times' => self::TYPE_STRING,
            'ip_address' => self::TYPE_STRING,

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
    public function getSessions()
    { 
        return $this->sessions;
    }

    /**
     * @param mixed $sessions
     */
    public function setSessions($sessions)
    {
        $this->sessions = $sessions;
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
    public function getTimes()
    { 
        return $this->times;
    }

    /**
     * @param mixed $times
     */
    public function setTimes($times)
    {
        $this->times = $times;
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