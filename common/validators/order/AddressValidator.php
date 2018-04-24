<?php
namespace common\validators\order;

use common\models\Validator;
use common\models\parts\Address;

class AddressValidator extends Validator{

    public $message;
    /**
     * 指定时验证当前地址是否属于该用户
     */
    public $userId = false;

    protected function validateValue($addressId){
        try{
            $address = new Address(['id' => $addressId]);
        }catch(\Exception $e){
            return $this->message;
        }
        if($this->userId === false)return true;
        return $address->userId == $this->userId ? true : $this->message;
    }
}
