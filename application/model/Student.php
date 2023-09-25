<?php

/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/

namespace application\model;

use application\core\BaseModel;

class Student extends BaseModel
{
    public static $tableName = 'student';
    public $std_code;
    public $name;
    public $surname;
    public $birth_date;
    public $image_name;
    public $major_id;
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    {
        //not use audit info 
        $this->setAuditInfo(false);

        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'std_code' => self::TYPE_STRING,
            'name' => self::TYPE_STRING,
            'surname' => self::TYPE_STRING,
            'birth_date' => self::TYPE_DATE,
            'image_name' => self::TYPE_STRING,
            'major_id' => self::TYPE_INTEGER,
        ));

        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'name' => self::TYPE_STRING,
            'surname' => self::TYPE_STRING,
            'birth_date' => self::TYPE_DATE,
            'image_name' => self::TYPE_STRING,
            'major_id' => self::TYPE_INTEGER,
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
