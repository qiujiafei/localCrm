<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damagedcommodity\models\del\db;

use common\ActiveRecord\PickingCommodityAR;
use Yii;

class Del extends PickingCommodityAR {

     //删除
    public static function delEmployee(array $condition) {

        $damagedcommoditytype= PickingCommodityAR::deleteAll($condition);
        if (!$damagedcommoditytype) {
            return false;
        }
        return true;
    }

}
