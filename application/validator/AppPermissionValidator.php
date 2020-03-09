<?php
namespace application\validator;

use application\core\AppBaseValidator as BaseValidate;
use application\model\AppPermission as AppPermission;
class AppPermissionValidator extends BaseValidate
{
    public function __construct(AppPermission $appPermission)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $appPermission;
        $this->validateField('name', self::VALIDATE_REQUIRED);
        $this->validateField('description', self::VALIDATE_REQUIRED);
        //Custom Validate
        /*
        if($appPermission->getPrice < $appPermission->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}