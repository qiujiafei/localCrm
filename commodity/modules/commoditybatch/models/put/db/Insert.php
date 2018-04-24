<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commoditybatch\models\put\db;

use common\ActiveRecord\CommodityStockAR;
use Yii;

class Insert extends CommodityStockAR {

    //添加
    public static function insertCommodityUnit(array $data) {

        try {
            $unitInsert = new self;

            foreach ($data as $k => $v) {
                $unitInsert->$k = $v;
            }

            $unitInsert->save(false);
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   //验证数据存在不存在
    public static function getField(array $condition, $field) {
        
        return CommodityStockAR::find()->select($field)->where($condition)->one();
        
    }

}
