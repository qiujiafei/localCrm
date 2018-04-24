<?php
namespace common\validators\custom;


use common\ActiveRecord\CustomUserAR;
use common\models\Validator;


class AccountMobileValidator extends Validator{

    public $message;
    /**
     * 指定时验证当前地址是否属于该用户
     */
    public $type;

    public $message_exists;


    protected function validateValue($mobile){

        if($this->type>=0) {

            //找回密码时，验证手机号码是否存在
            if($this->type==1){
                return CustomUserAR::find()->where(['mobile'=>$mobile])->exists()?true:$this->message;
            }elseif($this->type==2||$this->type==0){
                return CustomUserAR::find()->where(['mobile'=>$mobile])->exists()?$this->message_exists:true;
            }
            //不作验证
            return true;
        }else{
            return CustomUserAR::find()->where(['mobile'=>$mobile])->exists()?$this->message:true;
        }
     }
}
