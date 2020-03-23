<?php

namespace application\model;

use application\core\AppModel;
use application\util\DateUtils;

class AppUserAccessTokens extends AppModel
{
    public static $tableName = 'app_user_access_tokens';

    private $app_user;
    private $name;
    private $revoked;
    private $expires_at;
    private $api_client;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_STRING,
            'app_user' => self::TYPE_INTEGER,
            'name' => self::TYPE_STRING,
            'revoked' => self::TYPE_INTEGER,
            'api_client' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_date' => self::TYPE_DATE_TIME,
            'expires_at' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'app_user' => self::TYPE_INTEGER,
            'name' => self::TYPE_STRING,
            'revoked' => self::TYPE_INTEGER,
            'api_client' => self::TYPE_INTEGER,
            'expires_at' => self::TYPE_DATE_TIME,
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
    public function getAppUser()
    { 
        return $this->app_user;
    }

    /**
     * @param int $app_user
     */
    public function setAppUser($app_user)
    {
        $this->app_user = $app_user;
    }

    /**
     * @return string
     */
    public function getName()
    { 
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRevoked()
    { 
        return $this->revoked;
    }

    /**
     * @param int $revoked
     */
    public function setRevoked($revoked)
    {
        $this->revoked = $revoked;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    { 
        if(!empty($this->expires_at)){
            return $this->expires_at;
        }
        return DateUtils::getDateNow(true);
    }

    /**
     * @param mixed $expires_at
     */
    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;
    }

    /**
     * @return mixed
     */
    public function getApiClient()
    {
        return $this->api_client;
    }

    /**
     * @param mixed $api_client
     */
    public function setApiClient($api_client)
    {
        $this->api_client = $api_client;
    }

}