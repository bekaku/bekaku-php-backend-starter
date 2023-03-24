<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/

namespace application\model;

use application\core\BaseModel;
class StudentImage extends BaseModel
{
    public static $tableName = 'student_image';
    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    { 
       //not use audit info 
        $this->setAuditInfo(false); 
 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'student_id' => self::TYPE_INTEGER,
            'image_name' => self::TYPE_STRING,
            'upload_user' => self::TYPE_INTEGER,
            'upload_date' => self::TYPE_DATE_TIME,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'student_id' => self::TYPE_INTEGER,
            'image_name' => self::TYPE_STRING,
            'upload_user' => self::TYPE_INTEGER,
            'upload_date' => self::TYPE_DATE_TIME,
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