<?php
namespace common\validators\order;

use common\models\Validator;
use common\models\parts\Order;

class NoValidator extends Validator{

    public $message;
    public $customerId = false;
    public $supplierId = false;

    protected function validateValue($no){
        try{
            $order = new Order(['orderNumber' => $no]);
            if($this->customerId !== false){
                if($this->customerId == $order->customerId){
                    $result = true;
                }else{
                    $result = $this->message;
                }
            }else{
                $result = true;
            }
            if($result !== true)return $result;
            if($this->supplierId !== false){
                if($this->supplierId != $order->supplierId){
                    $result = $this->message;
                }
            }
            return $result;
        }catch(\Exception $e){
            return $this->message;
        }
    }
}
