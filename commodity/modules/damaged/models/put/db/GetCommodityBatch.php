<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\put\db;

use common\ActiveRecord\CommodityBatchAR;
use Yii;

class GetCommodityBatch extends CommodityBatchAR {

    public static function getField(array $condition, $field) {

        return CommodityBatchAR::find()->select($field)->where($condition)->asArray()->one();
        
    }

}
