<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\put\db;

use common\ActiveRecord\ServiceClassificationAR;
use Yii;

class Get extends ServiceClassificationAR {

    
     //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return ServiceClassificationAR::find()->select($field)->where($condition)->one();
    }

    

}
