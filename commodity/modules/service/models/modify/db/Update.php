<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\modify\db;

use common\ActiveRecord\ServiceAR;
use Yii;

class Update extends ServiceAR {

    //更改
    public static function modifyService(array $condition, array $modify_data) {

        try {
            $modify = ServiceAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() == false) {
                throw new \Exception('服务项目更改失败', 9030);
                return false;
            }
            return true;

            //更改商品表的关联单位名
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //批量更改
    public static function modifyAllService(array $condition, array $modify_data) {

        try {

            return ServiceAR::updateAll($modify_data, $condition);
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
