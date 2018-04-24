<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\put\db;

use common\ActiveRecord\MemberAR;
use Yii;

class InsertMember extends MemberAR {

    //更改
    public static function InsertMemberData(array $data) {
        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }

            $Insert->save(false);

            return $Insert->id;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return MemberAR::find()->select($field)->where($condition)->one();
    }

}
