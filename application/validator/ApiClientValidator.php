<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\ApiClient;
class ApiClientValidator extends BaseValidator
{
    public function __construct(ApiClient $apiClient)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $apiClient;
        $this->validateField('api_name', self::VALIDATE_REQUIRED);
        $this->validateField('api_token', self::VALIDATE_REQUIRED);
        $this->validateField('by_pass', self::VALIDATE_BOOLEAN);
        $this->validateField('status', self::VALIDATE_BOOLEAN);

        //Custom Validate
        /*
        if($apiClient->getPrice < $apiClient->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}