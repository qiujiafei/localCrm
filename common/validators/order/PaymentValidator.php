<?php
namespace common\validators\order;

use common\models\Validator;
use common\models\parts\trade\PaymentMethodInterface;
use yii\base\InvalidConfigException;

class PaymentValidator extends Validator{

    public $message;
    //必须指定支付方式对象
    public $paymentMethod;

    protected function validateValue($paymentMethod){
        if($this->paymentMethod instanceof PaymentMethodInterface){
            return $this->paymentMethod::canPay($paymentMethod) ? : $this->message;
        }else{
            throw new InvalidConfigException;
        }
    }
}
