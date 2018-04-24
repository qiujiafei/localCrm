<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace feature\modules\employeetype\models\Del\db;

use common\ActiveRecord\EmployeeAR;
use Yii;

class Veyrify extends EmployeeAR {

    //验证工种有没有关联的员工
    public static function veyrifyTypeEmployee(array $condition) {

        $info = EmployeeAR::find()->where($condition)->one();
        if ($info) {
            return false;
        }
        return true;
    }

}
