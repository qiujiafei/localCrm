<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\del\db;

use common\ActiveRecord\CustomerCarsTireBrandAR;
use Yii;

class Del extends CustomerCarsTireBrandAR {

    //删除
    public static function delCustomerCarsTireBrand(array $condition) {

        $del = CustomerCarsTireBrandAR::deleteAll($condition);
        if (!$del) {
            return false;
        }
        return true;
    }

}
