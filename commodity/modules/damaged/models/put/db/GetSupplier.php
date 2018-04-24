<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\put\db;

use common\ActiveRecord\SupplierAR;

use Yii;

class GetSupplier extends SupplierAR {

    
   public static function getField(array $condition, $field) {
        
        return SupplierAR::find()->select($field)->where($condition)->asArray()->one();
        
    }

    

}
