<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeetype\models\del\db;

use common\ActiveRecord\EmployeeTypeAR;
use common\ActiveRecord\EmployeeAR;
use Yii;

class Del extends EmployeeTypeAR {

    //删除
    public static function delEmployeeType(array $condition) {

        $employeetype = EmployeeTypeAR::deleteAll($condition);
        
        if ($employeetype < 0) {
             throw new \Exception('工种删除失败', 6008);
             return false;
        }
        return true;
    }

}
