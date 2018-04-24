<?php

namespace common\validators\admin;


use common\models\Validator;

class AdminEmployeeVaildator extends Validator
{
    public $message;//特殊字符
    public $messageSmall;//太短
    public $messageLong;//太长

    public function validateValue($value)
    {
        $name = trim($value['name']);
        if (isset($name))
        {
            if (strlen($name) > 15)
            {
                return $this->messageLong;
            }
            elseif (strlen($name) < 1)
            {
                return $this->messageSmall;
            }
            elseif (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $name))
            {
                return $this->message;
            }
            else
            {
                return true;
            }
        }
    }


}