<?php
namespace application\model;

use application\core\AppModel;
use application\util\UploadUtil;
use application\util\DateUtils;
class ApiClient extends AppModel
{
    public static $tableName = 'api_client';

    private $api_name;
    private $api_token;
    private $bypass = false;
    private $status = false;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'api_name' => self::TYPE_STRING,
            'api_token' => self::TYPE_STRING,
            'bypass' => self::TYPE_BOOLEAN,
            'status' => self::TYPE_BOOLEAN,
            'created_user' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'api_name' => self::TYPE_STRING,
            'api_token' => self::TYPE_STRING,
            'bypass' => self::TYPE_BOOLEAN,
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
     * @return string
     */
    public function getApiName()
    { 
        return $this->api_name;
    }

    /**
     * @param string $api_name
     */
    public function setApiName($api_name)
    {
        $this->api_name = $api_name;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    { 
        return $this->api_token;
    }

    /**
     * @param mixed $api_token
     */
    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;
    }

    /**
     * @return bool
     */
    public function isBypass()
    {
        return $this->bypass;
    }

    /**
     * @param bool $bypass
     */
    public function setBypass($bypass)
    {
        $this->bypass = $bypass;
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