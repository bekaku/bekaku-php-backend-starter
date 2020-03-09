<?php

namespace application\model;

use application\core\AppModel;
use application\util\UploadUtil;
use application\util\DateUtils;
class ApiClientIp extends AppModel
{
    public static $tableName = 'api_client_ip';

    private $api_client;
    private $api_address;
    private $status = false;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'api_client' => self::TYPE_INTEGER,
            'api_address' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,
            'created_user' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'api_client' => self::TYPE_INTEGER,
            'api_address' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,
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
     * @return int
     */
    public function getApiClient()
    { 
        return $this->api_client;
    }

    /**
     * @param int $api_client
     */
    public function setApiClient($api_client)
    {
        $this->api_client = $api_client;
    }

    /**
     * @return mixed
     */
    public function getApiAddress()
    { 
        return $this->api_address;
    }

    /**
     * @param mixed $api_address
     */
    public function setApiAddress($api_address)
    {
        $this->api_address = $api_address;
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

}