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
use common\models\parts\coupon\CouponRecord;
use common\models\Validator;

class CouponPasswordValidator extends Validator
{

    public $messageNotExists;
    public $message;
    public $code;
    public $customerId;



    public function validateValue($password)    {
        //如果
        $where="code='$this->code' and (custom_user_id='$this->customerId' or (custom_user_id='0' and status='".CouponRecord::STATUS_EXCITED."'))";
        if($rs=CouponRecordAR::find()->where($where)->select(['status','password'])->asArray()->one()){
            if($rs['password']!=$password){
                return $this->messageNotExists;
            }
            if($rs['status']!=CouponRecord::STATUS_EXCITED){
                return $this->message;
            }
            return true;
        }
        return $this->messageNotExists;

    }

}