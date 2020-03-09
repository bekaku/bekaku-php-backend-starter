<?php
namespace application\validator;

use application\core\AppBaseValidator as BaseValidate;
use application\model\AppUser as AppUser;
class AppUserValidator extends BaseValidate
{
    public function __construct(AppUser $appUser)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $appUser;
        $this->validateField('username', self::VALIDATE_REQUIRED);
        $this->validateField('email', self::VALIDATE_REQUIRED);

        //Custom Validate
        /*
        if($appUser->getPrice < $appUser->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}