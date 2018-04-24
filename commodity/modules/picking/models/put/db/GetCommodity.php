<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\picking\models\put\db;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityStockAR;

use Yii;

class GetCommodityStock extends CommodityAR {

    
   public static function getField(array $condition, $field) {
        
        return CommodityAR::find()->select($field)->where($condition)->one();
        
    }

    

}
