<?php

namespace application\model;

use application\core\BaseModel;

class ApiClientIp extends BaseModel
{
    public static $tableName = 'api_client_ip';
    public $status;
    public $ip_address;
    public $api_client;
    public $created_at;
    public $updated_at;
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    {
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'status' => self::TYPE_BOOLEAN,
            'ip_address' => self::TYPE_STRING,
            'api_client' => self::TYPE_STRING,
            'created_at' => self::TYPE_DATE_TIME,
            'updated_at' => self::TYPE_DATE_TIME,
        ));

        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'status' => self::TYPE_BOOLEAN,
            'ip_address' => self::TYPE_STRING,
            'api_client' => self::TYPE_STRING,
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
