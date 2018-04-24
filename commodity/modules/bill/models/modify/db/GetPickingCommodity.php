<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\modify\db;

use common\ActiveRecord\PickingAR;
use common\ActiveRecord\PickingCommodityAR;
use Yii;

class GetPickingCommodity extends PickingCommodityAR {
    //获取所有
    public static function getAllPicking(array $condition, $field) {

        return PickingAR::find()->select($field)->where($condition)->asArray()->all();
    }
   
    //获取所有
    public static function getAllPickingCommodity(array $condition, $field) {

        return PickingCommodityAR::find()->select($field)->where($condition)->asArray()->all();
    }

}
