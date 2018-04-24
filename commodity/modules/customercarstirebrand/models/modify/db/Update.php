<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\modify\db;

use common\ActiveRecord\CustomerCarsTireBrandAR;
use Yii;

class Update extends CustomerCarsTireBrandAR {

    //更改
    public static function modifyCustomerCarsTireBrand(array $condition, array $modify_customercarstirebrand_data) {


        try {

            $modify = CustomerCarsTireBrandAR::find()->where($condition)->one();

            if (!$modify) {
                $transaction->rollback();
                return false;
            }

            foreach ($modify_customercarstirebrand_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                throw new \Exception('轮胎品牌名修改失败', 15004);
                return false;
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
