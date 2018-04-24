<?php
namespace common\validators\ShoppingCart;

use common\models\Validator;
use common\ActiveRecord\ProductSKUAR;
use common\ActiveRecord\ShoppingCartAR;
use common\models\parts\Product;

class SkuIdValidator extends Validator{

    /**
     * 指定时验证Product ID
     */
    public $productId;
    /**
     * 指定时验证当前Item是否在该用户购物车内
     */
    public $userId;
    /**
     * 指定时验证商品库存是否大于该数量
     */
    public $count;

    public $message;

    protected function validateValue($skuId){
        if($ProductSKUAR = ProductSKUAR::findOne($skuId)){
            if($this->productId){
                if($this->productId != $ProductSKUAR->product_id)return $this->message;
            }
            $product = new Product(['id' => $ProductSKUAR->product_id]);
            if(!$product->onSale)return $this->message;
            if($this->count){
                if($this->count > $ProductSKUAR->stock)return $this->message;
                if($this->userId){
                    if($ShoppingCartAR = ShoppingCartAR::findOne(['custom_user_id' => $this->userId, 'product_sku_id' => $skuId])){
                        if($ShoppingCartAR->count + $this->count > $ProductSKUAR->stock)return $this->message;
                    }
                }
            }
            return true;
        }else{
            return $this->message;
        }
    }
}
