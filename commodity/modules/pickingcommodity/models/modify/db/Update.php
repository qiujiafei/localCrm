<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingcommodity\models\modify\db;

use common\ActiveRecord\PickingCommodityAR;
use Yii;

class Update extends PickingCommodityAR {

    //更改
    public static function modifyEmployee(array $condition, array $modify_pickingcommodity_data) {


        try {

            $modify = PickingCommodityAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_pickingcommodity_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                throw new \Exception('员工更改失败', 7014);
                return false;
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //批量更改
    public static function modifyAllEmployee(array $condition, array $modify_pickingcommodity_data) {

        try {

            return  PickingCommodityAR::updateAll($modify_pickingcommodity_data, $condition);
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
