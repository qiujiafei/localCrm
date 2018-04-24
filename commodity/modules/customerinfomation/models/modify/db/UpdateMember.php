<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\modify\db;

use common\ActiveRecord\MemberAR;
use Yii;

class UpdateMember extends MemberAR {

    //更改
    public static function modifyMember(array $condition, array $modify_data) {


        try {

            $modify = CustomerCarsAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }

            return $modify->save();
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
}
