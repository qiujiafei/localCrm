<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\del\db;

use common\ActiveRecord\DamagedAR;
use Yii;

class Del extends DamagedAR {

     //删除
    public static function delEmployee(array $condition) {

        $damagedtype= DamagedAR::deleteAll($condition);
        if (!$damagedtype) {
            return false;
        }
        return true;
    }

}
