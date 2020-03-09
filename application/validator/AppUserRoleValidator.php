<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\AppBaseValidator as BaseValidate;
use application\model\AppUserRole as AppUserRole;
class AppUserRoleValidator extends BaseValidate
{
    public function __construct(AppUserRole $appUserRole)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $appUserRole;
        $this->validateField('status', self::VALIDATE_INT);

        //Custom Validate
        /*
        if($appUserRole->getPrice < $appUserRole->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}