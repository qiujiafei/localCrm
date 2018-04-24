<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\models\modify\db;

use common\ActiveRecord\StoreAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class Update extends StoreAR
{
      //更改
    public static function modifyStore(array $condition, array $modify_data) {
        
        try {

            $modify = StoreAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                throw new \Exception('员工更改失败', 7014);
                return false;
            }
            
            return true;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //批量更改
    public static function modifyAllStore(array $condition, array $modify_data) {

        try {
            
            return  StoreAR::updateAll($modify_data, $condition);
            
        } catch (\Exception $ex) {
            
            throw $ex;
            
        }
    }
}
