<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:09 PM
 */

namespace application\model;

use application\core\AppModel as BaseModel;
class AppUserRole extends BaseModel
{
    public static $tableName = 'app_user_role';

    private $name;
    private $description;
    private $status = false;

    public function __construct($data = array())
    {
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,

            'created_user' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
        ));


        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'status' => self::TYPE_BOOLEAN,

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

    public function populateManual($data)
    {
        if($data) {
            if (array_key_exists('name', $data)) {
                $this->setName($data['name']);
            }
            if (array_key_exists('description', $data)) {
                $this->setDescription($data['description']);
            }
            if (array_key_exists('status', $data)) {
                $this->setStatus($data['status']);
            }
        }
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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