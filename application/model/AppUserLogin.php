<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppUserLogin extends BaseModel
{
    public static $tableName = 'app_user_login';

    private $loged_in_date;
    private $loged_ip;
    private $app_user;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'loged_in_date' => self::TYPE_STRING,
            'loged_ip' => self::TYPE_STRING,
            'app_user' => self::TYPE_STRING,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'loged_in_date' => self::TYPE_STRING,
            'loged_ip' => self::TYPE_STRING,
            'app_user' => self::TYPE_STRING,

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
    public function getLogedInDate()
    { 
        return $this->loged_in_date;
    }

    /**
     * @param mixed $loged_in_date
     */
    public function setLogedInDate($loged_in_date)
    {
        $this->loged_in_date = $loged_in_date;
    }

    /**
     * @return mixed
     */
    public function getLogedIp()
    { 
        return $this->loged_ip;
    }

    /**
     * @param mixed $loged_ip
     */
    public function setLogedIp($loged_ip)
    {
        $this->loged_ip = $loged_ip;
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

}