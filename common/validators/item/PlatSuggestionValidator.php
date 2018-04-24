<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/5
 * Time: 12:00
 */

namespace common\validators\item;


use common\models\Validator;

class PlatSuggestionValidator extends Validator
{

    public $message;


    public $reason;//订单ID

    public function validateValue($image)
    {

        if ($image && !empty($this->reason)) {
            return true;
        }
        return $this->message;

    }

}