<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\store\models\modify\db;

use common\ActiveRecord\StoreAR;
use common\ActiveRecord\EmployeeUserAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class Update extends StoreAR {

    //更改
    public static function modifyStore(array $condition, array $modify_data) {

        try {

            $modify = StoreAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_data as $k => $v) {
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
    public static function modifyAllEmployeeUser(array $condition, array $modify_data) {

        $transaction = Yii::$app->db->beginTransaction();

        try {

            if (EmployeeUserAR::updateAll($modify_data, $condition) === false) {
                throw new \Exception('门店禁用失败', 11006);
            }
            
            $transaction->commit();
            
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
    }

    public static function getEmployeeUserAll(array $condition, $field) {

        return EmployeeUserAR::find()->select($field)->where($condition)->asArray()->all();
    }

    public static function getEmployeeUserField(array $condition, $field) {

        return EmployeeUserAR::find()->select($field)->where($condition)->one();
    }

}
