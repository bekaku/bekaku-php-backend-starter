<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/

namespace application\model;

use application\core\BaseModel;
class EdrColleage extends BaseModel
{
    public static $tableName = 'edr_colleage';
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'ipaddress' => self::TYPE_STRING,
            'domain' => self::TYPE_STRING,
            'school_id' => self::TYPE_STRING,
            'college_code' => self::TYPE_STRING,
            'name_th' => self::TYPE_STRING,
            'name_en' => self::TYPE_STRING,
            'java_ipaddress' => self::TYPE_STRING,
            'php_ipaddress' => self::TYPE_STRING,
            'sockets_path' => self::TYPE_STRING,
            'use_std_api' => self::TYPE_INTEGER,
            'ssl_expire' => self::TYPE_DATE,
            'storage' => self::TYPE_INTEGER,
            'ram' => self::TYPE_INTEGER,
            'cpu' => self::TYPE_INTEGER,
            'status' => self::TYPE_BOOLEAN,
            'created_user' => self::TYPE_INTEGER,
            'created_at' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_at' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'ipaddress' => self::TYPE_STRING,
            'domain' => self::TYPE_STRING,
            'school_id' => self::TYPE_STRING,
            'college_code' => self::TYPE_STRING,
            'name_th' => self::TYPE_STRING,
            'name_en' => self::TYPE_STRING,
            'java_ipaddress' => self::TYPE_STRING,
            'php_ipaddress' => self::TYPE_STRING,
            'sockets_path' => self::TYPE_STRING,
            'use_std_api' => self::TYPE_INTEGER,
            'ssl_expire' => self::TYPE_DATE,
            'storage' => self::TYPE_INTEGER,
            'ram' => self::TYPE_INTEGER,
            'cpu' => self::TYPE_INTEGER,
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