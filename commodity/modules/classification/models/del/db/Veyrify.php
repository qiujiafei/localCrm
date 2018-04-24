<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\del\db;

use common\ActiveRecord\CommodityAR;
use Yii;

class Veyrify extends CommodityAR {

    //验证分类有没有关联的商品
    public static function verifyClassCommodity(array $condition) {

        $info = CommodityAR::find()->where($condition)->one();
        if ($info) {
            return false;
        }
        return true;
    }

}
