<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\put\db;

use common\ActiveRecord\CommodityAR;

use Yii;

class GetCommodity extends CommodityAR {

    
   public static function getField(array $condition, $field) {
        
        return CommodityAR::find()->select($field)->where($condition)->asArray()->one();
        
    }

    

}
