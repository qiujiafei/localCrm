<?php
namespace common\validators\order;

use common\models\Validator;
use common\models\parts\trade\RechargeMethodAbstract;
use yii\base\InvalidConfigException;

class RechargeValidator extends Validator{

    public $message;
    public $rechargeMethod;

    protected function validateValue($rechargeMethod){
        if($this->rechargeMethod instanceof RechargeMethodAbstract){
            return $this->rechargeMethod::canRecharge($rechargeMethod) ? true : $this->message;
        }else{
            throw new InvalidConfigException;
        }
    }
}
