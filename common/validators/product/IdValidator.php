<?php
namespace common\validators\product;

use common\models\Validator;
use common\ActiveRecord\ProductAR;

//商品id验证器
class IdValidator extends Validator{

    public $message;
    //商品所属用户
    public $userId;
    public $saleStatus;

    /**
     * 验证商品$id是否存在
     *
     * 设置$this->userId则验证商品所属用户ID
     *
     * @param integer $id product表主键
     */
    protected function validateValue($id){
        if(!is_numeric($id) || !is_int((int)$id) || $id < 1)return $this->message;
        if($product = ProductAR::findOne($id)){
            if($this->userId){
                if($product->supply_user_id != $this->userId)return $this->message;
            }
            if($this->saleStatus && $this->saleStatus = (array)$this->saleStatus){
                if(!in_array($product->sale_status, $this->saleStatus))return $this->message;
            }
            return true;
        }else{
            return $this->message;
        }
    }
}
