<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\models\put\db;

use common\ActiveRecord\AdminUserAR;
use common\ActiveRecord\AdminRolesAR;
use Yii;

class InsertModel
{

    //添加
    public static function insertAdminUser(array $data) {

        $Insert = new AdminUserAR();

        foreach ($data as $k => $v) {
            $Insert->$k = $v;
        }

        if ($Insert->save()){
            return $Insert->id;
        }else{
            throw new \Exception('账号删除失败', 1015);
        }
    }

    //验证会员卡号存在不存在
    public static function getField(array $condition, $field) {

        return AdminUserAR::find()->select($field)->where($condition)->one();

    }
}
