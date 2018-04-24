<?php
namespace common\validators\address;

use common\models\Validator;
use common\models\parts\Address;

class IdValidator extends Validator{

    public $message;
    /**
     * 指定时验证当前地址是否属于该用户
     */
    public $userId = false;
    /**
     * 指定时验证当前地址是否是默认地址
     */
    public $validateIsDefault = false;

    protected function validateValue($id){
        try{
            $address = new Address(['id' => $id]);
        }catch(\Exception $e){
            return $this->message;
        }
        if($this->userId !== false && $address->userId != $this->userId)return $this->message;
        if($this->validateIsDefault && $address->isDefault)return $this->message;
        return true;
    }
}
