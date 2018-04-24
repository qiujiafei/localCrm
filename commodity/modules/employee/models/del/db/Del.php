<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\models\del\db;

use common\ActiveRecord\EmployeeAR;
use Yii;

class Del extends EmployeeAR {

     //删除
    public static function delEmployee(array $condition) {

        $employeetype= EmployeeAR::deleteAll($condition);
        if (!$employeetype) {
            return false;
        }
        return true;
    }

}
