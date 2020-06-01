<?php
namespace application\validator;

use application\core\BaseValidator;
use application\model\EdrYoutubeMapper;
class EdrYoutubeMapperValidator extends BaseValidator
{
    public function __construct(EdrYoutubeMapper $edrYoutubeMapper)
    {
        //call parent construct
        parent::__construct();
        $this->objToValidate = $edrYoutubeMapper;
        $this->validateField('description', self::VALIDATE_REQUIRED);

        //Custom Validate
        /*
        if($edrYoutubeMapper->getPrice < $edrYoutubeMapper->getDiscount){
          $this->addError('price', 'Price Can't Must than Discount');
        }
        */
    }
}