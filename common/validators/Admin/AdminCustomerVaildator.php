<?php

namespace common\validators\admin;


use common\ActiveRecord\CustomUserAR;
use common\models\Validator;
use Yii;
use yii\helpers\ArrayHelper;

class AdminCustomerVaildator extends Validator
{
    public $message;
    public $mobileMessage;
    public $customUserId;
    public $emailAttribute;
    public $mobileAttribute;

    public function validateValue($value)
    {
        $where = ['id'=>$this->customUserId];
        if (Yii::$app->RQ->AR(new CustomUserAR())->scalar(['select'=>['mobile'],'where'=>$where])){
            return $this->mobileMessage;
        }elseif(strpos($this->emailAttribute,'@') && empty($this->mobileAttribute)){
            $where =  ArrayHelper::merge($where,['email'=>$value]);
        }elseif(is_numeric($this->mobileAttribute) && empty($this->emailAttribute)){
            $where = ArrayHelper::merge($where,['mobile_bak'=>$value]);
        }elseif (strpos($this->emailAttribute,'@') && is_numeric($this->mobileAttribute)){
            $where = ['and', 'id='.$this->customUserId, ['or', "email='".$this->emailAttribute."'", 'mobile_bak='.$this->mobileAttribute]];
        }else{
            return $this->message;
        }
        return  Yii::$app->RQ->AR(new CustomUserAR())->exists(['where'=> $where,'limit'=>1]) ? true :$this->message;

    }


}