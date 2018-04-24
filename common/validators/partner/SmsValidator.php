<?php
namespace common\validators\partner;

use common\models\Validator;
use admin\models\parts\sms\SmsCaptcha;

class SmsValidator extends Validator{

    public $message;

    public $mobile;

    protected function validateValue($code){
        return SmsCaptcha::validateCaptcha($this->mobile, $code) ? true : $this->message;
    }
}
