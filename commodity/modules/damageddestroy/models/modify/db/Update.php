<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damageddestroy\models\modify\db;

use common\ActiveRecord\DamagedDestroyAR;
use Yii;

class Update extends DamagedDestroyAR {

    //更改
    public static function modifyEmployee(array $condition, array $modify_damageddestroy_data) {


        try {

            $modify = DamagedDestroyAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_damageddestroy_data as $k => $v) {
                $modify->$k = $v;
            }

            return $modify->save();
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
}
