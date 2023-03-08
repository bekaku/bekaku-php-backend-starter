<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 6:53 PM
 */

namespace application\core;

use application\util\AppUtil as AppUtils;
use application\util\FilterUtils as FilterUtils;
use application\util\i18next;

class BaseValidator
{
    protected $objToValidate = null;
    private $validationErrors = array();
    private $defaultErrorMsg = null;

    // types of predefined validators:
    const VALIDATE_REQUIRED = 1;
    const VALIDATE_UNIQUE = 2;
    const VALIDATE_INT = 3;
    const VALIDATE_EMAIL = 4;
    const VALIDATE_NUMBER = 5;
    const VALIDATE_POSITIVE_NUMBER = 6;
    const VALIDATE_DATE_TIME = 7;
    const VALIDATE_DATE = 8;
    const VALIDATE_BOOLEAN = 9;

    public function __construct()
    {
        $this->defaultErrorMsg = array(
            self::VALIDATE_REQUIRED => i18next::getTranslation('error.validate_require'),
            self::VALIDATE_UNIQUE => i18next::getTranslation('error.validate_unique'),
            self::VALIDATE_INT => i18next::getTranslation('error.validate_int'),
            self::VALIDATE_EMAIL => i18next::getTranslation('error.validate_email'),
            self::VALIDATE_NUMBER => i18next::getTranslation('error.validate_number'),
            self::VALIDATE_POSITIVE_NUMBER => i18next::getTranslation('error.validate_positive_number'),
            self::VALIDATE_DATE_TIME => i18next::getTranslation('error.validate_datetime'),
            self::VALIDATE_DATE => i18next::getTranslation('error.validate_date'),
            self::VALIDATE_BOOLEAN => i18next::getTranslation('error.validate_boolean'),
        );
    }

    /**
     * Validate single field value with one of the predefined validators.
     * If not validated then add error to $this->validationErrors
     *
     * @param $fieldName string
     * @param $type int One of the constants for predefined validator
     * @param $message string Error message to be displayed or NULL for default message
     * @param $params array Additional params for validators that requre them, e.g.
     * min and max values for a range validator, etc.
     * @return bool Whether value has been validated correctly
     */
    public function validateField($fieldName, $type, $message = null, $params = null)
    {
        // here I use a switch statement on $type and perform all the necessary
        // validation checks using regular expressions and other methods
        // ...
        switch ($type) {
            case self::VALIDATE_REQUIRED:
                if (AppUtils::isEmpty($this->objToValidate->{$fieldName})) {
                    $this->addError($fieldName, $this->defaultErrorMsg[self::VALIDATE_REQUIRED]);
                }
                break;
            case self::VALIDATE_EMAIL:
                if (!FilterUtils::isValidEmail($this->objToValidate->{$fieldName})) {
                    $this->addError($fieldName, $this->defaultErrorMsg[self::VALIDATE_EMAIL]);
                }
                break;
            case self::VALIDATE_INT:
                if (!FilterUtils::isValidInt($this->objToValidate->{$fieldName})) {
                    $this->addError($fieldName, $this->defaultErrorMsg[self::VALIDATE_INT]);
                }
                break;
            case self::VALIDATE_DATE_TIME:
                if (!FilterUtils::isValidateDate($this->objToValidate->{$fieldName})) {
                    $this->addError($fieldName, $this->defaultErrorMsg[self::VALIDATE_DATE_TIME]);
                }
                break;
            case self::VALIDATE_DATE:
                if (!FilterUtils::isValidateDate($this->objToValidate->{$fieldName}, 'Y-m-d')) {
                    $this->addError($fieldName, $this->defaultErrorMsg[self::VALIDATE_DATE]);
                }
                break;
            case self::VALIDATE_BOOLEAN:
                if (!FilterUtils::isValidBoolean($this->objToValidate->{$fieldName})) {
                    $this->addError($fieldName, $this->defaultErrorMsg[self::VALIDATE_BOOLEAN]);
                }
                break;
        }
        return null;
    }

    /**
     * Add error to this object (to $this->validationErrors).
     * Used for manually validating what can't be validated
     * using the predefined validators.
     *
     * @param $fieldName string
     * @param $message string Error message to be displayed
     */
    public function addError($fieldName, $message)
    {
//        $addErrorMsg = array(
//            $fieldName => $message,
//        );
//        array_push($this->validationErrors, $addErrorMsg);
//        $this->validationErrors[$fieldName] = $message;
        array_push($this->validationErrors, ['field' => $fieldName, 'message' => $message]);
    }

    /**
     * Get validation errors for all fields that didn't pass validation.
     * The result is an array where keys are field names and values are
     * validation messages.
     *
     * @return array|NULL
     */
    public function getValidationErrors()
    {
        return count($this->validationErrors) > 0 ? ['errors' => $this->validationErrors] : array();
    }

    public static function getColunmValidatorByMysqlType($type)
    {
        switch ($type) {
            case BaseModel::MYSQL_TYPE_TINYINT:
            case BaseModel::MYSQL_TYPE_INT:
                return "self::VALIDATE_INT";
                break;
            case BaseModel::MYSQL_TYPE_DATETIME:
                return "self::VALIDATE_DATE_TIME";
                break;
            case BaseModel::MYSQL_TYPE_DATE:
                return "self::VALIDATE_DATE";
                break;
            default:
                return null;
        }
    }
}