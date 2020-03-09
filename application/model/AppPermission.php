<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppPermission extends BaseModel
{
    public static $tableName = 'app_permission';

    private $name;
    private $description;
    private $crud_table;

    private $status = true;

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'crud_table' => self::TYPE_STRING,
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
    /**
     * @return mixed
     */
    public function getCrudTable()
    {
        return $this->crud_table;
    }

    /**
     * @param mixed $crud_table
     */
    public function setCrudTable($crud_table)
    {
        $this->crud_table = $crud_table;
    }
}