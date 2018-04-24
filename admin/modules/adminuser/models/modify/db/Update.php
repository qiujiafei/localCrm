<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\models\modify\db;

use common\ActiveRecord\AdminUserAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class Update extends AdminUserAR
{

    public static function modifyAdminUser(array $condition, array $modify_data) {
        
        try {

            $modify = AdminUserAR::find()->where($condition)->one();

            if($modify) {
                if ($modify->type == 2){
                    throw new \Exception("该账户无法操作", 20008);
                }
                $modify->attributes = $modify_data;
                $modify->updateAttributes($modify_data);
                $result = $modify->save();
            } else {
                throw new \Exception("该用户不存在", 20001);
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
