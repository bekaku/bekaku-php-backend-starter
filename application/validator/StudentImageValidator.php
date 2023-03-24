<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\validator;

use application\core\BaseValidator;
use application\model\StudentImage;
class StudentImageValidator extends BaseValidator
{
    public function __construct(StudentImage $studentImage)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $studentImage;
        $this->validateField('student_id', self::VALIDATE_INT);
        $this->validateField('upload_user', self::VALIDATE_INT);
        $this->validateField('upload_date', self::VALIDATE_DATE_TIME);

        //Custom Validate
        /*
        if($studentImage->getPrice < $studentImage->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}