<?php
namespace common\validators;


use common\models\Validator;
use custom\models\parts\sms\SmsCaptcha;

class SmsValidator extends Validator{

    public $message;
    /**
     * 指定时验证当前地址是否属于该用户
     */
    public $mobile;

    protected function validateValue($code){
        return SmsCaptcha::validateCaptcha($this->mobile,$code)?true:$this->message;
     }
}
