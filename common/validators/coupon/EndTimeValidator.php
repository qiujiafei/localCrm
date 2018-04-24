<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 10:57
 */

namespace common\validators\coupon;


use common\models\Validator;

class EndTimeValidator extends Validator
{

    public $message;
    public $startTime;
    public $messageOutRange;


    public function validateValue($endTime)
    {
        if (strtotime($endTime)<time()) {
            return $this->messageOutRange;
        }
        if (strtotime($endTime) <= strtotime($this->startTime)) {
            return $this->message;
        }
        return true;
    }

}