<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\models\modify\db;

use common\ActiveRecord\UnitAR;
use common\ActiveRecord\CommodityAR;
use Yii;

class Update extends UnitAR {

    //更改
    public static function modifyUnit(array $condition, array $modify_data) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modify = UnitAR::find()->where($condition)->one();
            
            if (!$modify) {
                $transaction->rollback();
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }
            if ($modify->save() === false) {
                $transaction->rollback();
                return false;
            } else {
                $unit_name = array_key_exists('unit_name', $modify_data) ? $modify_data['unit_name'] : '';
                if ($unit_name) {
                    $condition['unit_id'] = $condition['id'];
                    unset($condition['id']);

                    $modify_commodity_data['unit_name'] = $unit_name;

                    $commodity_modify = CommodityAR::updateAll($modify_commodity_data, $condition);
                    if ($commodity_modify === false) {
                        $transaction->rollback();
                        return false;
                    }
                }
                $transaction->commit();
                return true;
            }
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
    }

}
