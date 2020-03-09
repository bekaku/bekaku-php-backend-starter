<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppUserRoleRoles extends BaseModel
{
    public static $tableName = 'app_user_role_roles';

    private $app_user;
    private $app_user_role;

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'app_user' => self::TYPE_STRING,
            'app_user_role' => self::TYPE_STRING,
 
 
            'created_user' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'app_user' => self::TYPE_STRING,
            'app_user_role' => self::TYPE_STRING,

            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
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
    public function getAppUserRole()
    { 
        return $this->app_user_role;
    }

    /**
     * @param mixed $app_user_role
     */
    public function setAppUserRole($app_user_role)
    {
        $this->app_user_role = $app_user_role;
    }

}