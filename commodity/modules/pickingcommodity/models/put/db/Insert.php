<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingcommodity\models\put\db;

use common\ActiveRecord\PickingCommodityAR;
use Yii;

class Insert extends PickingCommodityAR {

    //添加
    public static function insertPickingCommodity(array $data) {
        
        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }

            $Insert->save(false);
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    //批量添加
    public static function batchInsertPickingCommodity(array $field,array $data) {

        try {
            $picking_commodity=new PickingCommodityAR();
            return Yii::$app->db->createCommand()->batchInsert($picking_commodity::tableName(), $field, $data)->execute();
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   //验证数据存在不存在
    public static function getField(array $condition, $field) {
        
        return PickingCommodityAR::find()->select($field)->where($condition)->one();
        
    }

}
