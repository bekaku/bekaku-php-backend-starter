<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 9:15 PM
 */

namespace application\validator;

use application\core\BaseValidator;
use application\model\AppTable;
class AppTableValidator extends BaseValidator
{
    public function __construct(AppTable $appTable)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $appTable;
        $this->validateField('app_table_name', self::VALIDATE_REQUIRED);
//        $this->validateField('description', self::VALIDATE_REQUIRED);
    }
}