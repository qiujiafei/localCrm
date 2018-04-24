<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damageddestroy\models\del\db;

use common\ActiveRecord\PickingDestroyAR;
use Yii;

class Del extends PickingDestroyAR {

     //删除
    public static function delEmployee(array $condition) {

        $damageddestroytype= PickingDestroyAR::deleteAll($condition);
        if (!$damageddestroytype) {
            return false;
        }
        return true;
    }

}
