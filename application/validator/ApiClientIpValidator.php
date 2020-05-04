<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\ApiClientIp;
class ApiClientIpValidator extends BaseValidator
{
    public function __construct(ApiClientIp $apiClientIp)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $apiClientIp;
        $this->validateField('status', self::VALIDATE_INT);

        //Custom Validate
        /*
        if($apiClientIp->getPrice < $apiClientIp->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}