<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\Major;
class MajorValidator extends BaseValidator
{
    public function __construct(Major $major)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $major;

        //Custom Validate
        /*
        if($major->getPrice < $major->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}