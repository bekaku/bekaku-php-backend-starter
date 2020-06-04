<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\EdrColleage;
class EdrColleageValidator extends BaseValidator
{
    public function __construct(EdrColleage $edrColleage)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $edrColleage;
        $this->validateField('use_std_api', self::VALIDATE_BOOLEAN);
        $this->validateField('ssl_expire', self::VALIDATE_DATE);
        $this->validateField('storage', self::VALIDATE_INT);
        $this->validateField('ram', self::VALIDATE_INT);
        $this->validateField('cpu', self::VALIDATE_INT);
        $this->validateField('status', self::VALIDATE_BOOLEAN);

        //Custom Validate
        /*
        if($edrColleage->getPrice < $edrColleage->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}