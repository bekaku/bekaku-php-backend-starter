<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\EdrStdMapper;
class EdrStdMapperValidator extends BaseValidator
{
    public function __construct(EdrStdMapper $edrStdMapper)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $edrStdMapper;
        $this->validateField('function_name', self::VALIDATE_REQUIRED);

        //Custom Validate
        /*
        if($edrStdMapper->getPrice < $edrStdMapper->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}