<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 2/1/2016
 * Time: 8:56 PM
 */

namespace application\core;

use application\util\ControllerUtil as ControllerUtils;
use application\util\FilterUtils as FilterUtil;
use application\util\DateUtils as DateUtil;
use application\util\AppUtil as AppUtils;

class AppModel
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

    //mysql usual type
    const MYSQL_TYPE_CARCHAR = "varchar";
    const MYSQL_TYPE_INT = "int";
    const MYSQL_TYPE_TINYINT = "tinyint";
    const MYSQL_TYPE_DATE = "date";
    const MYSQL_TYPE_DATETIME = "datetime";

    private $tableField = array();
    private $tableFieldForEdit = array();
    private $tableBaseField = array('id', 'created_user', 'created_date', 'updated_user', 'updated_date');
    private $tableOptionalField = array();

    private $id;
    private $created_user;
    private $created_date;
    private $updated_user;
    private $updated_date;

    public function populateBase($dataArray)
    {

        foreach ($this->getTableBaseField() as $column) {
            $methodSetName = 'set' . AppUtils::genPublicMethodName($column);
            if (array_key_exists($column, $dataArray)) {
//                echo 'field='.$methodSetName.'<br>';
                $this->{$methodSetName}($dataArray[$column]);
            } else {
                if ($column != 'id') {
                    $this->{$methodSetName}(null);
                }
            }
        }
    }

    public function populateBaseManul($data)
    {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }
        if (array_key_exists('created_user', $data)) {
            $this->setCreatedUser($data['created_user']);
        } else {
            $this->setCreatedUser(null);
        }
        if (array_key_exists('created_date', $data)) {
            $this->setCreatedDate($data['created_date']);
        } else {
            $this->setCreatedDate(null);
        }
        if (array_key_exists('updated_user', $data)) {
            $this->setUpdatedUser($data['updated_user']);
        } else {
            $this->setUpdatedUser(null);
        }
        if (array_key_exists('updated_date', $data)) {
            $this->setUpdatedDate($data['updated_date']);
        } else {
            $this->setUpdatedDate(null);
        }
    }

    public function populate($dataArray, $object = null)
    {
        if ($dataArray && $object) {

            foreach ($this->getTableField() as $column => $dataType) {
                if (!in_array($column, $this->getTableBaseField())) {
                    $methodSetName = 'set' . AppUtils::genPublicMethodName($column);
                    if (array_key_exists($column, $dataArray)) {
                        $object->{$methodSetName}($dataArray[$column]);
                    }
                }
            }

            /* if have optional field */
            if (count($this->getTableOptionalField()) > 0) {
                foreach ($this->getTableOptionalField() as $optionColumn => $optionDataType) {
                    $optionMethodSetName = 'set' . AppUtils::genPublicMethodName($optionColumn);
                    if (array_key_exists($optionColumn, $dataArray)) {
                        $object->{$optionMethodSetName}($dataArray[$optionColumn]);
                    }
                }
            }

        }
    }

    public function populatePostData()
    {
        foreach ($this->getTableField() as $column => $dataType) {
            $methodSetName = 'set' . AppUtils::genPublicMethodName($column);
            if (!in_array($column, $this->getTableBaseField())) {
                $this->{$methodSetName}(self::initPostFilter($column, $dataType));
            }
        }

        //optional field
        if (count($this->getTableOptionalField()) > 0) {
            foreach ($this->getTableOptionalField() as $columnOption => $dataTypeOption) {
                $methodSetNameOption = 'set' . AppUtils::genPublicMethodName($columnOption);
                $this->{$methodSetNameOption}(self::initPostFilter($columnOption, $dataTypeOption));
            }
        }
    }

    public function populatePostDataRestApi($jsonData, $uid = null, $isUpdate = false)
    {
        foreach ($this->getTableField() as $column => $dataType) {
            $methodSetName = 'set' . AppUtils::genPublicMethodName($column);
            if (!in_array($column, $this->getTableBaseField())) {
                $this->{$methodSetName}(self::initVarFilter($jsonData->{$column}, $dataType));
            }
        }

        //optional field
        if (count($this->getTableOptionalField()) > 0) {
            foreach ($this->getTableOptionalField() as $columnOption => $dataTypeOption) {
                $methodSetNameOption = 'set' . AppUtils::genPublicMethodName($columnOption);
                $this->{$methodSetNameOption}(self::initVarFilter($jsonData->{$columnOption}, $dataTypeOption));
            }
        }

        //for update
        if ($isUpdate) {
            if (isset($jsonData->id)) {
                $this->setId($jsonData->id);
            }
            $this->setUpdatedUser($uid);
            $this->setUpdatedDate(DateUtil::getDateNow());
        } else {
            $this->setCreatedUser($uid);
            $this->setCreatedDate(DateUtil::getDateNow());
            $this->setUpdatedUser($uid);
            $this->setUpdatedDate(DateUtil::getDateNow());
        }

    }

    public static function getColunmTypeStringByMysqlType($type, $extra = '')
    {
        switch ($type) {
            case self::MYSQL_TYPE_CARCHAR:
                return "self::TYPE_STRING";
                break;
            case self::MYSQL_TYPE_INT:
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
            case self::MYSQL_TYPE_CARCHAR:
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
        return $this->tableBaseField;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCreatedUser()
    {
        return $this->created_user;
    }

    /**
     * @param mixed $created_user
     */
    public function setCreatedUser($created_user)
    {
        if (FilterUtil::filterVarInt($created_user)) {
            $this->created_user = $created_user;
        } else {
            $this->created_user = ControllerUtils::getUserIdSession();
        }
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date)
    {
        if (FilterUtil::filterVarUnsafe($created_date)) {
            $this->created_date = $created_date;
        } else {
            $this->created_date = DateUtil::getDateNow();
        }
    }

    /**
     * @return mixed
     */
    public function getUpdatedUser()
    {
        return $this->updated_user;
    }

    /**
     * @param mixed $updated_user
     */
    public function setUpdatedUser($updated_user)
    {
        if (FilterUtil::filterVarInt($updated_user)) {
            $this->updated_user = $updated_user;
        } else {
            $this->updated_user = ControllerUtils::getUserIdSession();
        }
    }

    /**
     * @return mixed
     */
    public function getUpdatedDate()
    {
        return $this->updated_date;
    }

    /**
     * @param mixed $updated_date
     */
    public function setUpdatedDate($updated_date)
    {
        if (FilterUtil::filterVarUnsafe($updated_date)) {
            $this->updated_date = $updated_date;
        } else {
            $this->updated_date = DateUtil::getDateNow();
        }
    }

}