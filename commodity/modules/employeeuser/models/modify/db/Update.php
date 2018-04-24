<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\modify\db;

use common\ActiveRecord\EmployeeUserAR;
use Yii;

class Update extends EmployeeUserAR {

    //更改
    public static function modifyEmployeeUser(array $condition, array $modify_data) {


        try {

            $modify = EmployeeUserAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                throw new \Exception('账号更改失败', 7014);
                return false;
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //批量更改
    public static function modifyAllEmployeeUser(array $condition, array $modify_data) {

        try {

            return  EmployeeUserAR::updateAll($modify_data, $condition);
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
