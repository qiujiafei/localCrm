<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingdestroy\models\put\db;

use common\ActiveRecord\PurchaseAR;
use Yii;

class Get extends PurchaseAR {

    
    //获取总数
    public static function getSum(array $condition, $field) {

        return PurchaseAR::find()->select($field)->where($condition)->sum();
        
    }

    

}
