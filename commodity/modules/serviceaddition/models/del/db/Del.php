<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\del\db;

use common\ActiveRecord\ServiceAdditionAR;
use Yii;

class Del extends ServiceAdditionAR {

     //删除
    public static function delServiceAddition(array $condition) {

        $del= ServiceAdditionAR::deleteAll($condition);
        if ($del<0) {
            throw new \Exception('附加项目删除失败', 9009);
            return false;
        }
        return true;
    }

}
