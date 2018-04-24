<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 10:57
 */

namespace common\validators\coupon;


use common\ActiveRecord\CouponRecordAR;
use common\ActiveRecord\SupplyUserAR;
use common\models\Validator;

class CouponIdExistValidator extends Validator
{

    public $message;
    public $customerId;
    public $status;



    public function validateValue($id){
        if($id==0){
            return true;
        }
        $where="id='$id' and custom_user_id='$this->customerId' and status='$this->status'";
        if(CouponRecordAR::find()->where($where)->exists()){
            return true;
        }
        return $this->message;
    }

}