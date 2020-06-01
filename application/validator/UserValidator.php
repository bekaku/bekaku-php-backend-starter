<?php

namespace application\validator;

use application\core\BaseValidator;
use application\model\User;

class UserValidator extends BaseValidator
{
    public function __construct(User $user)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $user;
        $this->validateField('email', self::VALIDATE_REQUIRED);
        $this->validateField('username', self::VALIDATE_REQUIRED);

        //Custom Validate
        /*
        if($user->getPrice < $user->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}