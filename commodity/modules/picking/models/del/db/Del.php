<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\picking\models\del\db;

use common\ActiveRecord\PickingAR;
use Yii;

class Del extends PickingAR {

     //删除
    public static function delEmployee(array $condition) {

        $pickingtype= PickingAR::deleteAll($condition);
        if (!$pickingtype) {
            return false;
        }
        return true;
    }

}
