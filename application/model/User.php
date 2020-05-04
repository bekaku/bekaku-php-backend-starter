<?php

namespace application\model;

use application\core\BaseModel;
class User extends BaseModel
{
    public static $tableName = 'user';
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'username' => self::TYPE_STRING,
            'salt' => self::TYPE_STRING,
            'email' => self::TYPE_STRING,
            'image' => self::TYPE_STRING,
            'password' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,

            'created_at' => self::TYPE_DATE_TIME,
            'updated_at' => self::TYPE_DATE_TIME,
            'created_user' => self::TYPE_STRING,
            'updated_user' => self::TYPE_STRING,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'username' => self::TYPE_STRING,
            'email' => self::TYPE_STRING,
            'image' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,

            'updated_user' => self::TYPE_INTEGER,
            'updated_at' => self::TYPE_DATE_TIME
        ));

        /* init optional field*/
        $this->setTableOptionalField(array(
            //'field_name_option',
        ));

        $this->populate($jsonData, $this, $uid, $isUpdate);
    }

    public static function getTableName()
    {
        return self::$tableName;
    }

}