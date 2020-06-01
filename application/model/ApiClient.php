<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/

namespace application\model;

use application\core\BaseModel;
class ApiClient extends BaseModel
{
    public static $tableName = 'api_client';
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'api_name' => self::TYPE_STRING,
            'api_token' => self::TYPE_STRING,
            'by_pass' => self::TYPE_INTEGER,
            'status' => self::TYPE_BOOLEAN,
            'created_at' => self::TYPE_DATE_TIME,
            'updated_at' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'created_user' => self::TYPE_INTEGER,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'api_name' => self::TYPE_STRING,
            'api_token' => self::TYPE_STRING,
            'by_pass' => self::TYPE_INTEGER,
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