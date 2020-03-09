<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:09 PM
 */

namespace application\model;

use application\core\AppModel;
class AppTable extends AppModel
{
    public static $tableName = 'app_table';

    private $app_table_name="user_log";
    private $description="Just Test";
    private $vtheme;

    public function __construct($data = array())
    {
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'app_table_name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'vtheme' => self::TYPE_STRING,

            'created_user' => self::TYPE_INTEGER,
            'created_date' => self::TYPE_DATE_TIME,
            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
        ));


        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'app_table_name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'vtheme' => self::TYPE_STRING,

            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
        ));

        $this->populate($data, $this);
        $this->populateBase($data);
    }

    /**
     * @return mixed
     */
    public function getVtheme()
    {
        return $this->vtheme;
    }

    /**
     * @param mixed $vtheme
     */
    public function setVtheme($vtheme)
    {
        $this->vtheme = $vtheme;
    }

    public static function getTableName()
    {
        return self::$tableName;
    }

    public function populateManual($data)
    {
        if($data) {
            if (array_key_exists('app_table_name', $data)) {
                $this->setName($data['app_table_name']);
            }
            if (array_key_exists('description', $data)) {
                $this->setDescription($data['description']);
            }
        }
    }
    /**
     * @return mixed
     */
    public function getAppTableName()
    {
        return $this->app_table_name;
    }

    /**
     * @param mixed $app_table_name
     */
    public function setAppTableName($app_table_name)
    {
        $this->app_table_name = $app_table_name;
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
}