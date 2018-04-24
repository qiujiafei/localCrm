<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\del\db;

use common\ActiveRecord\EmployeeUserAR;
use Yii;

class Del extends EmployeeUserAR {

    //删除
    public static function delEmployee(array $condition) {

        $del = EmployeeUserAR::deleteAll($condition);
        if ($del < 0) {
            throw new \Exception('账号删除失败', 1015);
            return false;
        }
        return true;
    }

}
