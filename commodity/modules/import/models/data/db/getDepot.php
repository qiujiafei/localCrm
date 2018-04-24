<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\import\models\data\db;

use common\ActiveRecord\DepotAR;
use Yii;

class getDepot extends DepotAR {

    //验证数据存在不存在
    public static function getField(array $condition, $field) {
        
        return DepotAR::find()->select($field)->where($condition)->one();
        
    }

}
