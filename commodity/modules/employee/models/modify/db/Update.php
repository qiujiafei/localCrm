<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\models\modify\db;

use common\ActiveRecord\EmployeeAR;
use Yii;

class Update extends EmployeeAR {

    //更改
    public static function modifyEmployee(array $condition, array $modify_employee_data) {


        try {

            $modify = EmployeeAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_employee_data as $k => $v) {
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
    public static function modifyAllEmployee(array $condition, array $modify_employee_data) {

        try {

            return  EmployeeAR::updateAll($modify_employee_data, $condition);
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
