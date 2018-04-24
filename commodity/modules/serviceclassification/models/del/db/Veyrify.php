<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceclassification\models\del\db;

use common\ActiveRecord\ServiceAR;
use Yii;

class Veyrify extends ServiceAR {

    //验证工种有没有关联的员工
    public static function veyrifyService(array $condition) {

        $info = ServiceAR::find()->where($condition)->one();
        if ($info) {
            return false;
        }
        return true;
    }

}
