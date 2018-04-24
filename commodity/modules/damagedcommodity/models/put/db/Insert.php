<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damagedcommodity\models\put\db;

use common\ActiveRecord\DamagedCommodityAR;
use Yii;

class Insert extends DamagedCommodityAR {

    //添加
    public static function insertDamagedCommodity(array $data) {

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
    public static function batchInsertDamagedCommodity(array $field,array $data) {

        try {
            $damaged_commodity=new DamagedCommodityAR();
            return Yii::$app->db->createCommand()->batchInsert($damaged_commodity::tableName(), $field, $data)->execute();
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return DamagedCommodityAR::find()->select($field)->where($condition)->one();
    }

}
