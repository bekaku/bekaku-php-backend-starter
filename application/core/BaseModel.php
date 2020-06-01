<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 2/1/2016
 * Time: 8:56 PM
 */

namespace application\core;

use application\util\AppUtil as AppUtils;
use application\util\FilterUtils as FilterUtil;
use application\util\DateUtils as DateUtil;

class BaseModel
{
    const TYPE_AUTO_INCREMENT = 99;
    const TYPE_STRING = 1;
    const TYPE_INTEGER = 2;
    const TYPE_FLOAT = 3;
    const TYPE_BOOLEAN = 4;
    const TYPE_ARRAY = 5;
    const TYPE_OBJECT = 6;
    const TYPE_NULL = 7;
    const TYPE_HTML = 8;
    const TYPE_NO_FILTER = 9;
    const TYPE_DATE = 10;
    const TYPE_DATE_TIME = 11;
    const TYPE_BIT = 12;
    const TYPE_TEXT_AREA = 13;
    const TYPE_IMAGE = 14;

    //mysql usual type
    const MYSQL_TYPE_VARCHAR = "varchar";
    const MYSQL_TYPE_INT = "int";
    const MYSQL_TYPE_BIG_INT = "bigint";
    const MYSQL_TYPE_TINYINT = "tinyint";
    const MYSQL_TYPE_DATE = "date";
    const MYSQL_TYPE_DATETIME = "datetime";

    private $tableField = array();
    private $tableFieldForEdit = array();
    private $tableBaseField = array('id', 'created_user', 'created_at', 'updated_user', 'updated_at');
    private $tableBaseFieldExceptAudit = array('id');
    private $tableOptionalField = array();

    private $auditInfo = true;


    public function populate($jsonData = null, $object = null, $uid = null, $isUpdate = false)
    {
        if ($jsonData && $object) {
            foreach ($this->getTableField() as $column => $dataType) {

                if (in_array($column, $this->getTableBaseField())) {

                    if ($column == 'created_user' || $column == 'updated_user') {
                        if (!$isUpdate) {
                            $object->{'created_user'} = $uid;
                        }
                        $object->{'updated_user'} = $uid;
                    }

                    if ($column == 'created_at' || $column == 'updated_at') {
                        if (!$isUpdate) {
                            $object->{'created_at'} = DateUtil::getDateNow();
                        }
                        $object->{'updated_at'} = DateUtil::getDateNow();
                    }
                    if ($column == 'id' && $isUpdate) {
                        if (isset($jsonData->id)) {
                            $object->{'id'} = $jsonData->id;
                        }
                    }
                } else {
                    if (isset($jsonData->{$column})) {
                        $object->{$column} = self::initVarFilter($jsonData->{$column}, $dataType);
                    }
                }
            }


        }
    }

    public function populatePostData()
    {
        foreach ($this->getTableField() as $column => $dataType) {
            if (!in_array($column, $this->getTableBaseField())) {
                $this->{$column} = self::initPostFilter($column, $dataType);
            }
        }

        //optional field
        if (count($this->getTableOptionalField()) > 0) {
            foreach ($this->getTableOptionalField() as $columnOption => $dataTypeOption) {
                $this->{$columnOption} = self::initPostFilter($columnOption, $dataTypeOption);
            }
        }
    }

    public static function getColunmTypeStringByMysqlType($type, $extra = '')
    {
        switch ($type) {
            case self::MYSQL_TYPE_VARCHAR:
                return "self::TYPE_STRING";
                break;
            case self::MYSQL_TYPE_INT:
            case self::MYSQL_TYPE_BIG_INT:
                if ($extra == 'auto_increment') {
                    return "self::TYPE_AUTO_INCREMENT";
                } else {
                    return "self::TYPE_INTEGER";
                }
                break;
            case self::MYSQL_TYPE_TINYINT:
                return "self::TYPE_INTEGER";
                break;
            case self::MYSQL_TYPE_DATETIME:
                return "self::TYPE_DATE_TIME";
                break;
            case self::MYSQL_TYPE_DATE:
                return "self::TYPE_DATE";
                break;
            default:
                return "self::TYPE_STRING";
        }
    }

    public static function getColunmGetSetTypeByMysqlType($type)
    {
        switch ($type) {
            case self::MYSQL_TYPE_VARCHAR:
                return "string";
                break;
            case self::MYSQL_TYPE_INT:
            case self::MYSQL_TYPE_TINYINT:
                return "int";
                break;
            case self::MYSQL_TYPE_DATETIME:
            case self::MYSQL_TYPE_DATE:
                return "mixed";
                break;
            default:
                return "mixed";
        }
    }

    private function initVarFilter($val, $inputTyp)
    {
        switch ($inputTyp) {
            case self::TYPE_STRING:
            case self::TYPE_DATE:
            case self::TYPE_DATE_TIME:
                return FilterUtil::filterVarString($val);
                break;
            case self::TYPE_INTEGER:
                if (!empty(FilterUtil::filterVarInt($val))) {
                    return FilterUtil::filterVarInt($val);
                } else {
                    return 0;
                }
                break;
            case self::TYPE_FLOAT:
                return FilterUtil::filterVarFloat($val);
                break;
            case self::TYPE_BOOLEAN:
                if (!empty(FilterUtil::filterVarInt($val))) {
                    return FilterUtil::filterVarInt($val);
                } else {
                    return 0;
                }
            case self::TYPE_HTML:
                return FilterUtil::filterVarSpecialChar($val);
                break;
            case self::TYPE_NO_FILTER:
                return null;
                break;
            default:
                return false;
        }
    }

    private function initPostFilter($inputName, $inputTyp)
    {
        switch ($inputTyp) {
            case self::TYPE_STRING:
            case self::TYPE_DATE:
            case self::TYPE_DATE_TIME:
                return FilterUtil::filterPostString($inputName);
                break;
            case self::TYPE_INTEGER:
                if (!empty(FilterUtil::filterPostInt($inputName))) {
                    return FilterUtil::filterPostInt($inputName);
                } else {
                    return 0;
                }
                break;
            case self::TYPE_FLOAT:
                return FilterUtil::filterPostFloat($inputName);
                break;
            case self::TYPE_BOOLEAN:
                if (!empty(FilterUtil::filterPostInt($inputName))) {
                    return FilterUtil::filterPostInt($inputName);
                } else {
                    return 0;
                }
            case self::TYPE_HTML:
                return FilterUtil::filterPostSpecialChar($inputName);
                break;
            case self::TYPE_NO_FILTER:
                return null;
                break;
            default:
                return false;
        }
    }

    /**
     * @return array
     */
    public function getTableBaseField()
    {
        if ($this->auditInfo) {
            return $this->tableBaseField;
        }
        return $this->tableBaseFieldExceptAudit;
    }

    public function getAutoIncrementType()
    {
        return self::TYPE_AUTO_INCREMENT;
    }

    public function getBooleanType()
    {
        return self::TYPE_BOOLEAN;
    }

    public function getIntType()
    {
        return self::TYPE_INTEGER;
    }

    public function getBitType()
    {
        return self::TYPE_BIT;
    }

    /**
     * @param array $tableField
     */
    public function setTableField($tableField)
    {
        $this->tableField = $tableField;
    }

    public function getTableField()
    {
        return $this->tableField;
    }

    /**
     * @return array
     */
    public function getTableFieldForEdit()
    {
        return $this->tableFieldForEdit;
    }

    /**
     * @param array $tableFieldForEdit
     */
    public function setTableFieldForEdit($tableFieldForEdit)
    {
        $this->tableFieldForEdit = $tableFieldForEdit;
    }

    /**
     * @return array
     */
    public function getTableOptionalField()
    {
        return $this->tableOptionalField;
    }

    /**
     * @param array $tableOptionalField
     */
    public function setTableOptionalField($tableOptionalField)
    {
        $this->tableOptionalField = $tableOptionalField;
    }

    /**
     * @return bool
     */
    public function isAuditInfo()
    {
        return $this->auditInfo;
    }

    /**
     * @param bool $auditInfo
     */
    public function setAuditInfo($auditInfo)
    {
        $this->auditInfo = $auditInfo;
    }
}