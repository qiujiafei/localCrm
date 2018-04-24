<?php
namespace common\validators\item;

use common\models\Validator;
use common\ActiveRecord\ShoppingCartAR;
use common\ActiveRecord\ProductSKUAR;
use common\models\parts\Item;

class IdValidator extends Validator{

    public $message;
    /**
     * 指定时验证当前商品是否在该用户的购物车内
     */
    public $userId = false;
    /**
     * 指定时检查商品库存是否大于该数量
     */
    public $count;

    protected function validateValue($id){
        if($this->userId === false){
            return ProductSKUAR::findOne($id) ? true : $this->message;
        }else{
            if(!$shoppingCartAR = ShoppingCartAR::findOne(['custom_user_id' => $this->userId, 'product_sku_id' => $id]))return $this->message;
            if(is_null($this->count)){
                return true;
            }else{
                if($this->count === true){
                    $count = $shoppingCartAR->count;
                }else{
                    $count = (int)$this->count;
                }
                if($count > 0){
                    $item = new Item(['id' => $id]);
                    return $item->stock >= $count ? : $this->message;
                }else{
                    return $this->message;
                }
            }
        }
    }
}
