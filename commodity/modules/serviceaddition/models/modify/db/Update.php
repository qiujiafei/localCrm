<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\modify\db;

use common\ActiveRecord\ServiceAdditionAR;
use Yii;

class Update extends ServiceAdditionAR {

    //更改
    public static function modifyServiceAddition(array $condition, array $modify_data) {

        try {
            $modify = ServiceAdditionAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }
            if ($modify->save() === false) {
                throw new \Exception('附加项目更改失败', 9007);
                return false;
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
