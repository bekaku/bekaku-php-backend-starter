<?php
namespace application\model;

use application\core\BaseModel;
class AccessToken extends BaseModel
{
    public static $tableName = 'access_token';
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'user_agent' => self::TYPE_INTEGER,
            'token' => self::TYPE_STRING,
            'api_client' => self::TYPE_STRING,
            'user' => self::TYPE_STRING,
            'revoked' => self::TYPE_INTEGER,
            'created_at' => self::TYPE_DATE_TIME,
            'expires_at' => self::TYPE_DATE_TIME,
            'updated_at' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'user_agent' => self::TYPE_STRING,
            'token' => self::TYPE_STRING,
            'api_client' => self::TYPE_STRING,
            'user' => self::TYPE_STRING,
            'revoked' => self::TYPE_INTEGER,
            'expires_at' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
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