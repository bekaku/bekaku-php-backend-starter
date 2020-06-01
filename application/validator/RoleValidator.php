<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\Role;
class RoleValidator extends BaseValidator
{
    public function __construct(Role $role)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $role;
        $this->validateField('name', self::VALIDATE_REQUIRED);
        $this->validateField('status', self::VALIDATE_BOOLEAN);

        //Custom Validate
        /*
        if($role->getPrice < $role->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}