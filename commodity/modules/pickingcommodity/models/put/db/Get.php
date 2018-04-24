<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingcommodity\models\put\db;

use common\ActiveRecord\PickingCommodityAR;
use Yii;

class Get extends PickingCommodityAR {

    
    //获取总数
    public static function getSum(array $condition, $field) {

        return PickingCommodityAR::find()->select($field)->where($condition)->sum();
        
    }

    

}
