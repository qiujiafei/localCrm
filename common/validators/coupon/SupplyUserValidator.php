<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 10:57
 */

namespace common\validators\coupon;


use common\ActiveRecord\SupplyUserAR;
use common\models\Validator;

class SupplyUserValidator extends Validator
{

    public $message;


    public function validateValue($supply_user_id)
    {
        //如果
        if($supply_user_id==0)return true;
        if(!SupplyUserAR::find()->where(['id'=>$supply_user_id])->exists()){
            return $this->message;
        }
        return true;
    }

}