<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingdestroy\models\modify\db;

use common\ActiveRecord\PickingDestroyAR;
use Yii;

class Update extends PickingDestroyAR {

    //更改
    public static function modifyEmployee(array $condition, array $modify_pickingdestroy_data) {


        try {

            $modify = PickingDestroyAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_pickingdestroy_data as $k => $v) {
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
    public static function modifyAllEmployee(array $condition, array $modify_pickingdestroy_data) {

        try {

            return  PickingDestroyAR::updateAll($modify_pickingdestroy_data, $condition);
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
