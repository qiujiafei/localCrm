<?php
/**
 * User: JiangYi
 * Date: 2017/5/26
 * Time: 18:40
 * Desc:
 */

namespace common\validators\item;


use common\models\Validator;
use custom\models\parts\ItemInCart;
use Yii;
class ItemsValidator extends Validator
{

    public $message;
    public $stockOverFlowMessage;//库存不足
    public $userId;

    public function validateValue($var)
    {
        try{
            if($var["item_id"]==$var["sku_id"]){
                //不更新商品属性，仅更新数量
                $item=new ItemInCart(['id'=>$var['item_id']]);
                //取当前sku库存数量
                $stock=$item->getProductObj()->getSKU()->getSKU()[$item->getSKU()]['stock'];
                if($var["quantity"]>$stock){
                    return $this->stockOverFlowMessage;
                }
            }else{
                //验证新属性库存
                $item=new ItemInCart(['id'=>$var["sku_id"]]);
                $stock=$item->getProductObj()->getSKU()->getSKU()[$item->getSKU()]['stock'];
                if($var["quantity"]>$stock){
                    return $this->stockOverFlowMessage;
                }
            }
            return true;
        }catch (\Exception $e){
            return $this->message;
        }



    }


}