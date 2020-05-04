<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:09 PM
 */

namespace application\model;

use application\core\BaseModel;
class AppTable extends BaseModel
{
    public static $tableName = 'app_table';

    public function __construct(\stdClass $jsonData = null, $uid = null, $isUpdate = false)
    {

        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'app_table_name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'vtheme' => self::TYPE_STRING,
        ));


        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'app_table_name' => self::TYPE_STRING,
            'description' => self::TYPE_STRING,
            'vtheme' => self::TYPE_STRING,
        ));

        $this->populate($jsonData, $this, $uid, $isUpdate);
    }
}