<?php
namespace common\validators\business;

use common\models\Validator;

class StatusValidator extends Validator{

    public $message;
    protected function validateValue($status){
        $statusArr = [1,2,3,4,5];
        if (strpos(',',$status)){
            if(!array_diff(explode(',',$status),$statusArr)) return true;
        }else{
            if(in_array($status,$statusArr)) return true;
        }
        return $this->message;
    }
}
