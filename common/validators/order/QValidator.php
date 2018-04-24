<?php
namespace common\validators\order;

use Yii;
use common\models\Validator;
use custom\models\parts\UrlParamCrypt;
use common\models\parts\Item;
use common\ActiveRecord\ShoppingCartAR;
use common\models\parts\Product;

class QValidator extends Validator{

    public $message;
    /**
     * 指定时验证Items是否在该用户的购物车内
     */
    public $userId;
    /**
     * 指定时验证商品库存是否大于用户购物车内商品数量
     */
    public $validateStock = false;
    /**
     * 库存不足时返回的错误信息
     */
    public $outOfStock;

    /**
     * 验证q参数解密出的Items是否存在
     */
    protected function validateValue($q){
        if(!$itemsId = (new UrlParamCrypt)->decrypt($q))return $this->message;
        Yii::$app->RQ->queryMaster = true;
        try{
            $items = array_map(function($itemId){
                return new Item(['id' => $itemId]);
            }, $itemsId);
        }catch(\Exception $e){
            return $this->returnCallback($this->message);
        }
        if(is_null($this->userId))return $this->returnCallback(true);
        foreach($items as $item){
            if(!$AR = ShoppingCartAR::findOne([
                'custom_user_id' => $this->userId,
                'product_sku_id' => $item->id,
            ]))return $this->returnCallback($this->message);
            if($this->validateStock){
                if($item->saleStatus != Product::SALE_STATUS_ONSALE)return $this->returnCallback($this->outOfStock);
                if($AR->count > $item->stock)return $this->returnCallback($this->outOfStock);
            }
        }
        return $this->returnCallback(true);
    }

    protected function returnCallback($callback){
        Yii::$app->RQ->queryMaster = false;
        return $callback;
    }
}
