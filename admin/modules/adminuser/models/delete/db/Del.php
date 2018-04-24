<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\adminuser\models\delete\db;

use common\ActiveRecord\AdminUserAR;
use common\ActiveRecord\AdminRolesAR;
use common\ActiveRecord\AdminUserRolesAR;
use Yii;

class Del 
{
    public static function delAdminUser(array $condition) {
        return AdminUserAR::deleteAll($condition);
    }

    public static function isExists(int $id){
        return AdminUserAR::find()->where(['id'=>$id])->one();
    }

}
