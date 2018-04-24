<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\models\put\db;

use common\ActiveRecord\AdminUserAR;
use Yii;

class Insert extends AdminUserAR
{

    //添加
    public static function insertStore(array $data) {

        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }
            
            if($Insert->save(false)===false){
                return false;
            }
            
            return $Insert->id;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return AdminUserAR::find()->select($field)->where($condition)->one();

    }
}
