<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\del\db;

use common\ActiveRecord\CustomerInfomationAR;
use common\ActiveRecord\CustomerInfomationCarsAR;
use common\ActiveRecord\CustomerCarsAR;
use common\ActiveRecord\MemberAR;
use Yii;

class Del extends CustomerInfomationAR {

    //删除
    public static function delCustomerInfomation(array $condition) {
        $transaction = Yii::$app->db->beginTransaction();
        $customer_condition['store_id'] = $condition['store_id'];
        $customer_condition['customer_infomation_id'] = $condition['id'];
       
        if (CustomerInfomationAR::deleteAll($condition) === false) {
            $transaction->rollback();
            return false;
        } else {
            if (CustomerInfomationCarsAR::deleteAll($customer_condition) === false) {
                $transaction->rollback();
                return false;
            } else {
                if (CustomerCarsAR::deleteAll($customer_condition) === false) {
                    $transaction->rollback();
                    return false;
                } else {
                    if (MemberAR::deleteAll($customer_condition) === false) {
                        $transaction->rollback();
                        return false;
                    }
                }
            }
        }
        $transaction->commit();
        return true;
    }

}
