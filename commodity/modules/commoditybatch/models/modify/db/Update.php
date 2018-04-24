<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commoditybatch\models\modify\db;

use common\ActiveRecord\CommodityBatchAR;
use Yii;

class Update extends CommodityBatchAR {

    /**
     * 更改库存
     */
    public static function modifyStock(array $condition, array $modify_data) {
        try {
            $modify = CommodityBatchAR::find()->where($condition)->one();
            if (!$modify) {
                return false;
            }
            return $modify->updateCounters($modify_data); //updateCounters 实现 减法 值：给负数  ['num'=>"-$value[shop_num]"]
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
