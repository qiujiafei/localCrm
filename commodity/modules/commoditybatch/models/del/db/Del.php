<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commoditybatch\models\del\db;

use common\ActiveRecord\CommodityStockAR;
use Yii;

class Del extends CommodityStockAR {

    //删除
    public static function delCommodityUnit(array $condition) {

        $unit = CommodityStockAR::deleteAll($condition);
        if ($unit<0) {
            throw new \Exception('单位删除失败', 3004);
            return false;
        }
        return true;
    }

}
