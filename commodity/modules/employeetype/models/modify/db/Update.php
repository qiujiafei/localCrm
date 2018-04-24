<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeetype\models\modify\db;

use common\ActiveRecord\EmployeeTypeAR;
use common\ActiveRecord\EmployeeAR;
use Yii;

class Update extends EmployeeTypeAR {

    //更改
    public static function modifyEmployeetype(array $condition, array $modify_employeetype_data) {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modify = EmployeeTypeAR::find()->where($condition)->one();

            if (!$modify) {
                $transaction->rollback();
                return false;
            }

            foreach ($modify_employeetype_data as $k => $v) {
                $modify->$k = $v;
            }
           
            if ($modify->save() === false) {
                $transaction->rollback();
                return false;
            } else {
                $name = array_key_exists('name', $modify_employeetype_data) ? $modify_employeetype_data['name'] : '';
                if ($name) {
                    $condition['employee_type_id'] = $condition['id'];
                    unset($condition['id']);
 
                    $modify_employee_data['employee_type_name'] = $name;
                    
                    $employee_modify = EmployeeAR::updateAll($modify_employee_data, $condition);
                    if ($employee_modify === false) {
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
